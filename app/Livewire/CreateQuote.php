<?php

namespace App\Livewire;

use App\Models\Modules;
use App\Models\QuoteItems;
use App\Models\QuoteTemplates;
use App\Models\Quotes;
use App\Models\Organisations;
use App\Services\QuoteCalculator;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;

class CreateQuote extends Component
{
    public string $moduleSlug = 'fencing';

    public string $customerName = '';

    public string $jobName = '';

    public string $length = '';

    public string $labourRate = '';

    public string $markup = '';

    public string $waste = '';

    public string $vatRate = '';

    public string $paymentTerms = '';

    public string $whatsappPhone = '';

    public string $whatsappMessage = '';

    public function mount(): void
    {
        $this->applyDefaultFormValues();
        $this->jobName = $this->defaultJobName();

        $templateId = request()->integer('template');
        if ($templateId > 0) {
            $this->loadTemplate($templateId);
        }
    }

    private function applyDefaultFormValues(): void
    {
        $organisationId = (int) (auth()->user()?->organisation_id ?? 0);

        $global = config('quotes.form_defaults.global', []);
        $module = config('quotes.form_defaults.modules.'.$this->moduleSlug, []);

        $organisationDefaults = $this->loadOrganisationFormDefaults($organisationId);
        $organisationGlobal = is_array($organisationDefaults['global'] ?? null)
            ? $organisationDefaults['global']
            : [];
        $organisationModule = is_array($organisationDefaults['modules'][$this->moduleSlug] ?? null)
            ? $organisationDefaults['modules'][$this->moduleSlug]
            : [];

        // Precedence: organisation module > organisation global > config module > config global
        $defaults = array_merge($global, $module, $organisationGlobal, $organisationModule);

        $this->length = (string) ($defaults['length'] ?? '15');
        $this->labourRate = (string) ($defaults['labour_rate'] ?? '35');
        $this->markup = (string) ($defaults['markup'] ?? '15');
        $this->waste = (string) ($defaults['waste'] ?? '8');
        $this->vatRate = (string) ($defaults['vat_rate'] ?? '20');
        $this->paymentTerms = (string) ($defaults['payment_terms'] ?? '');
        $this->whatsappPhone = (string) ($defaults['whatsapp_phone'] ?? '+44');
    }

    private function loadOrganisationFormDefaults(int $organisationId): array
    {
        if ($organisationId <= 0) {
            return [];
        }

        $organisation = Organisations::query()->find($organisationId);

        if (! $organisation) {
            return [];
        }

        $defaults = $organisation->quote_defaults;

        return is_array($defaults) ? $defaults : [];
    }

    public function updatedLength(): void
    {
        if ($this->jobName === '' || Str::startsWith($this->jobName, 'Fence job -')) {
            $this->jobName = $this->defaultJobName();
        }
    }

    public function saveQuote(): void
    {
        $this->validate($this->rules());

        $user = auth()->user();
        $breakdown = $this->getBreakdown((int) ($user->organisation_id ?? 0));

        $quote = $this->createQuoteRecord($breakdown, (int) $user->id, (int) ($user->organisation_id ?? 0));
        $pdfBytes = $this->renderPdfBytes($quote, $breakdown, (int) $user->id, (string) $user->email, (string) $user->name);
        $this->persistQuotePdf($quote, $pdfBytes);

        $this->dispatch('toast', message: 'Quote saved successfully. PDF snapshot attached.', type: 'success');
    }

    public function saveTemplate(): void
    {
        $this->validate($this->rules());

        $user = auth()->user();
        $breakdown = $this->getBreakdown((int) ($user->organisation_id ?? 0));

        $module = Modules::query()->firstOrCreate(
            ['slug' => $this->moduleSlug],
            ['name' => Str::title($this->moduleSlug)]
        );

        QuoteTemplates::query()->create([
            'organisation_id' => (int) ($user->organisation_id ?? 0),
            'created_by' => (int) $user->id,
            'name' => $this->jobName !== '' ? $this->jobName : $this->defaultJobName(),
            'module_id' => (int) $module->id,
            'variant_key' => $this->moduleSlug,
            'data' => [
                'customer_name' => $this->customerName,
                'job_name' => $this->jobName,
                'length' => $breakdown['length'],
                'labour_rate' => $breakdown['labour_rate'],
                'markup' => $breakdown['markup'],
                'waste' => $breakdown['waste'],
                'vat_rate' => $breakdown['vat_rate'],
                'payment_terms' => $this->paymentTerms,
            ],
        ]);

        $this->dispatch('toast', message: 'Template saved successfully.', type: 'success');
    }

    public function prepareWhatsApp(): void
    {
        $this->validate($this->rules());

        $user = auth()->user();
        $breakdown = $this->getBreakdown((int) ($user->organisation_id ?? 0));
        $quote = $this->createQuoteRecord($breakdown, (int) $user->id, (int) ($user->organisation_id ?? 0));

        $customerName = trim($this->customerName) !== '' ? trim($this->customerName) : 'there';
        $total = '£'.number_format($breakdown['total_price'], 2);
        $quoteLink = route('quote.public', ['uuid' => $quote->uuid]);

        $this->whatsappMessage = "Hi {$customerName},\n\nHere's your quote for the fencing work:\n\nTotal: {$total}\n\nYou can view the full breakdown here:\n{$quoteLink}\n\nLet me know if you'd like to go ahead 👍";

        $this->dispatch('open-whatsapp-modal');
    }

    public function downloadPdf()
    {        
        $this->validate($this->rules());

        $user = auth()->user();
        $breakdown = $this->getBreakdown((int) ($user->organisation_id ?? 0));
        $quote = $this->createQuoteRecord($breakdown, (int) $user->id, (int) ($user->organisation_id ?? 0));
        $pdfBytes = $this->renderPdfBytes($quote, $breakdown, (int) $user->id, (string) $user->email, (string) $user->name);
        $this->persistQuotePdf($quote, $pdfBytes);

        $fileName = 'tradepulse-quote-'.$quote->id.'.pdf';

        return response()->streamDownload(function () use ($pdfBytes): void {
            echo $pdfBytes;
        }, $fileName, ['Content-Type' => 'application/pdf']);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.create-quote', [
            'breakdown' => $this->getBreakdown((int) (auth()->user()?->organisation_id ?? 0)),
        ]);
    }

    private function createQuoteRecord(array $breakdown, int $userId, int $organisationId): Quotes
    {
        $module = Modules::query()->firstOrCreate(
            ['slug' => $this->moduleSlug],
            ['name' => Str::title($this->moduleSlug)]
        );

        return DB::transaction(function () use ($breakdown, $organisationId, $userId, $module): Quotes {
            $quote = Quotes::query()->create([
                'organisation_id' => $organisationId,
                'created_by' => $userId,
                'module_id' => (int) $module->id,
                'variant_key' => Str::slug($this->jobName !== '' ? $this->jobName : $this->defaultJobName()),
                'customer_name' => $this->customerName !== '' ? $this->customerName : null,
                'job_name' => $this->jobName !== '' ? $this->jobName : $this->defaultJobName(),
                'status' => 'draft',
                'length' => $breakdown['length'],
                'labour_type' => 'per_metre',
                'labour_total' => (int) round($breakdown['labour_total']),
                'materials_total' => (int) round($breakdown['materials_total']),
                'subtotal_price' => (int) round($breakdown['subtotal']),
                'vat_rate' => $breakdown['vat_rate'],
                'vat_total' => (int) round($breakdown['vat_amount']),
                'payment_terms' => trim($this->paymentTerms) !== '' ? trim($this->paymentTerms) : null,
                'calculation_data' => $breakdown,
                'total_price' => (int) round($breakdown['total_price']),
            ]);

            $this->storeQuoteItems($quote, $breakdown);

            return $quote;
        });
    }

    private function storeQuoteItems(Quotes $quote, array $breakdown): void
    {
        $items = is_array($breakdown['items'] ?? null) ? $breakdown['items'] : [];

        if ($items === []) {
            return;
        }

        $rows = [];

        foreach ($items as $item) {
            $rows[] = [
                'quote_id' => (int) $quote->id,
                'module_item_id' => isset($item['module_item_id']) ? (int) $item['module_item_id'] : null,
                'name' => (string) ($item['name'] ?? 'Quote item'),
                'quantity' => (int) round((float) ($item['quantity'] ?? 0)),
                'unit_price' => (int) round((float) ($item['unit_price'] ?? 0)),
                'total_price' => (int) round((float) ($item['total'] ?? 0)),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        QuoteItems::query()->insert($rows);
    }

    private function persistQuotePdf(Quotes $quote, string $pdfBytes): void
    {
        $path = 'quotes/'.$quote->id.'/quote-'.now()->format('Ymd-His').'.pdf';

        Storage::disk('public')->put($path, $pdfBytes);

        $quote->forceFill([
            'pdf_path' => $path,
            'pdf_generated_at' => now(),
        ])->save();
    }

    private function renderPdfBytes(Quotes $quote, array $breakdown, int $userId, string $fallbackEmail, string $fallbackBusinessName): string
    {
        $organisation = null;

        if ((int) $quote->organisation_id > 0) {
            $organisation = Organisations::query()->find((int) $quote->organisation_id);
        }

        $businessName = trim((string) ($organisation?->name ?? ''));
        if ($businessName === '') {
            $businessName = trim($fallbackBusinessName) !== '' ? trim($fallbackBusinessName) : 'TradePulse Customer';
        }

        $contactPhone = trim((string) ($organisation?->phone ?? ''));
        $contactEmail = $fallbackEmail;
        $logoPath = trim((string) ($organisation?->logo ?? ''));

        $logoDataUri = null;
        if ($logoPath !== '') {
            $absolutePath = public_path(ltrim($logoPath, '/'));
            if (is_file($absolutePath) && is_readable($absolutePath)) {
                $extension = strtolower((string) pathinfo($absolutePath, PATHINFO_EXTENSION));
                $mimeType = match ($extension) {
                    'png' => 'image/png',
                    'jpg', 'jpeg' => 'image/jpeg',
                    'gif' => 'image/gif',
                    default => null,
                };

                if ($mimeType !== null) {
                    $bytes = file_get_contents($absolutePath);
                    if ($bytes !== false) {
                        $logoDataUri = 'data:'.$mimeType.';base64,'.base64_encode($bytes);
                    }
                }
            }
        }

        $isPro = $this->isProUser($userId);

        $html = view('pdf.quote', [
            'businessName' => $businessName,
            'contactPhone' => $contactPhone,
            'contactEmail' => $contactEmail,
            'logoDataUri' => $logoDataUri,
            'customerName' => trim((string) ($quote->customer_name ?? '')),
            'jobDescription' => trim((string) ($quote->job_name ?? '')) !== ''
                ? trim((string) $quote->job_name)
                : 'Supply and install fencing - '.rtrim(rtrim(number_format($breakdown['length'], 2, '.', ''), '0'), '.').'m',
            'breakdown' => $breakdown,
            'paymentTerms' => trim((string) ($quote->payment_terms ?? '')),
            'isPro' => $isPro,
            'quoteDate' => $quote->created_at ?? now(),
            'validUntil' => ($quote->created_at ?? now())->copy()->addDays(14),
        ])->render();

        $dompdf = new Dompdf();
        $dompdf->set_option('isRemoteEnabled', true);
        $dompdf->set_option('isHtml5ParserEnabled', true);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }

    private function loadTemplate(int $templateId): void
    {
        $user = auth()->user();

        $template = QuoteTemplates::query()
            ->whereKey($templateId)
            ->where('created_by', (int) $user->id)
            ->when((int) ($user->organisation_id ?? 0) > 0, function ($query) use ($user) {
                $query->where('organisation_id', (int) $user->organisation_id);
            })->first();

        if (! $template) {
            return;
        }

        $data = is_array($template->data) ? $template->data : [];

        $this->jobName = (string) ($data['job_name'] ?? $template->name ?? $this->defaultJobName());
        $this->customerName = (string) ($data['customer_name'] ?? '');
        $this->length = (string) ($data['length'] ?? $this->length);
        $this->labourRate = (string) ($data['labour_rate'] ?? $this->labourRate);
        $this->markup = (string) ($data['markup'] ?? $this->markup);
        $this->waste = (string) ($data['waste'] ?? $this->waste);
        $this->vatRate = (string) ($data['vat_rate'] ?? $this->vatRate);
        $this->paymentTerms = (string) ($data['payment_terms'] ?? $this->paymentTerms);

        $this->dispatch('toast', message: 'Template loaded. Tweak values and save your quote.', type: 'info');
    }

    private function defaultJobName(): string
    {
        return 'Fence job - '.rtrim(rtrim(number_format($this->toFloat($this->length), 2, '.', ''), '0'), '.').'m';
    }

    private function rules(): array
    {
        return [
            'customerName' => ['nullable', 'string', 'max:150'],
            'jobName' => ['nullable', 'string', 'max:150'],
            'length' => ['required', 'numeric', 'min:0.1', 'max:10000'],
            'labourRate' => ['required', 'numeric', 'min:0', 'max:10000'],
            'markup' => ['required', 'numeric', 'min:0', 'max:200'],
            'waste' => ['required', 'numeric', 'min:0', 'max:100'],
            'vatRate' => ['required', 'numeric', 'min:0', 'max:100'],
            'paymentTerms' => ['nullable', 'string', 'max:1000'],
        ];
    }

    private function getBreakdown(int $organisationId): array
    {
        return app(QuoteCalculator::class)->calculate([
            'organisation_id' => $organisationId,
            'module_slug' => $this->moduleSlug,
            'inputs' => [
                'length' => $this->toFloat($this->length),
                'labour_rate' => $this->toFloat($this->labourRate),
                'markup' => $this->toFloat($this->markup),
                'waste' => $this->toFloat($this->waste),
                'vat_rate' => $this->toFloat($this->vatRate),
            ],
        ]);
    }

    private function toFloat(string|int|float|null $value): float
    {
        return max(0, (float) $value);
    }

    private function isProUser(int $userId): bool
    {
        $activeStatuses = ['active', 'trialing', 'past_due'];

        return DB::table('subscriptions')
            ->where('user_id', $userId)
            ->whereIn('stripe_status', $activeStatuses)
            ->exists();
    }
}

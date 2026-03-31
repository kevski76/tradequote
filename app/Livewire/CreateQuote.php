<?php

namespace App\Livewire;

use App\Models\Modules;
use App\Models\QuoteTemplates;
use App\Models\Quotes;
use App\Models\Organisations;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;

class CreateQuote extends Component
{
    public string $customerName = '';

    public string $jobName = '';

    public string $length = '15';

    public string $labourRate = '35';

    public string $markup = '15';

    public string $waste = '8';

    public string $vatRate = '20';

    public string $paymentTerms = '';

    public ?string $notice = null;

    public function mount(): void
    {
        $this->jobName = $this->defaultJobName();

        $templateId = request()->integer('template');
        if ($templateId > 0) {
            $this->loadTemplate($templateId);
        }
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
        $breakdown = $this->calculateBreakdown();

        $quote = $this->createQuoteRecord($breakdown, (int) $user->id, (int) ($user->organisation_id ?? 0));
        $pdfBytes = $this->renderPdfBytes($quote, $breakdown, (int) $user->id, (string) $user->email, (string) $user->name);
        $this->persistQuotePdf($quote, $pdfBytes);

        $this->notice = 'Quote saved successfully. PDF snapshot attached.';
    }

    public function saveTemplate(): void
    {
        $this->validate($this->rules());

        $user = auth()->user();
        $breakdown = $this->calculateBreakdown();

        $module = Modules::query()->firstOrCreate(
            ['slug' => 'fencing'],
            ['name' => 'Fencing']
        );

        QuoteTemplates::query()->create([
            'organisation_id' => (int) ($user->organisation_id ?? 0),
            'created_by' => (int) $user->id,
            'name' => $this->jobName !== '' ? $this->jobName : $this->defaultJobName(),
            'module_id' => (int) $module->id,
            'variant_key' => 'fencing',
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

        $this->notice = 'Template saved successfully.';
    }

    public function downloadPdf()
    {
        $this->validate($this->rules());

        $user = auth()->user();
        $breakdown = $this->calculateBreakdown();
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
            'breakdown' => $this->calculateBreakdown(),
        ]);
    }

    private function createQuoteRecord(array $breakdown, int $userId, int $organisationId): Quotes
    {
        $module = Modules::query()->firstOrCreate(
            ['slug' => 'fencing'],
            ['name' => 'Fencing']
        );

        return Quotes::query()->create([
            'organisation_id' => $organisationId,
            'created_by' => $userId,
            'module_id' => (int) $module->id,
            'variant_key' => Str::slug($this->jobName !== '' ? $this->jobName : $this->defaultJobName()),
            'customer_name' => $this->customerName !== '' ? $this->customerName : null,
            'job_name' => $this->jobName !== '' ? $this->jobName : $this->defaultJobName(),
            'status' => 'draft',
            'length' => $breakdown['length'],
            'labour_type' => 'per_metre',
            'labour_total' => (int) round($breakdown['labour_cost']),
            'materials_total' => (int) round($breakdown['materials_cost']),
            'subtotal_price' => (int) round($breakdown['subtotal']),
            'vat_rate' => $breakdown['vat_rate'],
            'vat_total' => (int) round($breakdown['vat_amount']),
            'payment_terms' => trim($this->paymentTerms) !== '' ? trim($this->paymentTerms) : null,
            'calculation_data' => $breakdown,
            'total_price' => (int) round($breakdown['total_price']),
        ]);
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
            })
            ->first();

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

        $this->notice = 'Template loaded. Tweak values and save your quote.';
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

    private function calculateBreakdown(): array
    {
        $length = $this->toFloat($this->length);
        $labourRate = $this->toFloat($this->labourRate);
        $markup = $this->toFloat($this->markup);
        $waste = $this->toFloat($this->waste);
        $vatRate = $this->toFloat($this->vatRate);

        $posts = $length > 0 ? ((int) ceil($length / 1.8)) + 1 : 0;
        $boards = $length > 0 ? (int) ceil($length * 9) : 0;

        $postUnitPrice = 18.00;
        $boardUnitPrice = 4.00;

        $materialsBase = ($posts * $postUnitPrice) + ($boards * $boardUnitPrice);
        $materialsCost = round($materialsBase * (1 + ($waste / 100)), 2);
        $labourCost = round($length * $labourRate, 2);
        $subtotal = round($materialsCost + $labourCost, 2);
        $markedUpSubtotal = round($subtotal * (1 + ($markup / 100)), 2);
        $vatAmount = round($markedUpSubtotal * ($vatRate / 100), 2);
        $totalPrice = round($markedUpSubtotal + $vatAmount, 2);

        return [
            'length' => $length,
            'labour_rate' => $labourRate,
            'markup' => $markup,
            'waste' => $waste,
            'vat_rate' => $vatRate,
            'posts_qty' => $posts,
            'posts_price' => round($posts * $postUnitPrice, 2),
            'boards_qty' => $boards,
            'boards_price' => round($boards * $boardUnitPrice, 2),
            'labour_cost' => $labourCost,
            'materials_cost' => $materialsCost,
            'subtotal' => $markedUpSubtotal,
            'vat_amount' => $vatAmount,
            'total_price' => $totalPrice,
        ];
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

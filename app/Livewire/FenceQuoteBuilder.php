<?php

namespace App\Livewire;

use App\Models\Modules;
use App\Models\QuoteItems;
use App\Models\QuoteTemplates;
use App\Models\Quotes;
use App\Models\Organisations;
use App\Services\QuoteService;
use App\Services\QuoteFormBuilder;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class FenceQuoteBuilder extends Component
{
    private const ALLOWED_HEIGHTS = [1.5, 1.8, 2.0];

    public string $moduleSlug = 'fencing';

    /**
     * For the fencing module: which covering type is active ('panels' or 'boards').
     * The inactive type is excluded from the Item Prices card and calculation.
     */
    public string $fencingType = 'panels';

    /**
     * Gate width in metres. When > 0 the effective fence length used for material
     * quantities is reduced by this amount: effective_length = length - gate_width.
     * Labour is always calculated against the full length.
     */
    public string $gateWidth = '0';
    public array $gates = [];

    /**
     * Pre-loaded module items with org prices, built once in mount().
     * Persisted in Livewire state so the DB is not re-queried on every render.
     *
     * @var array<int, array{module_item_id:int, key:string, name:string, type:string, calculation:string, formula_key:string, unit_price:float, quantity:int, enabled:bool}>
     */
    public array $formItems = [];

    /**
     * Per-item price and optional quantity overrides, keyed by module item key.
     * price    — unit price (pre-filled from org/DB prices, user-editable)
     * quantity — quantity override; 0 = auto-calculated from formula
     *
     * @var array<string, array{price:string|float|int, quantity:int, enabled:bool}>
     */
    public array $itemInputs = [];

    public array $autoResolvedItemPrices = [];

    public string $customerName = '';

    public string $customerPhone = '';

    public string $jobName = '';

    public string $length = '10';

    public string $height = '1.8';

    public string $labourRate = '35';

    public string $labourTotalOverride = '';

    public string $markup = '15';

    public string $waste = '8';

    public string $vatRate = '20';

    public string $paymentTerms = '';

    public bool $useMarkup = false;

    public string $whatsappPhone = '';

    public string $whatsappMessage = '';

    public function mount(): void
    {
        $this->applyDefaultFormValues();
        $this->height = $this->normalizeHeightValue($this->height);
        $this->jobName = $this->defaultJobName();

        $this->buildFormItems();

        $templateId = request()->integer('template');
        if ($templateId > 0) {
            $this->loadTemplate($templateId);
        }
    }

    /**
     * Load module items with organisation prices into $formItems.
     * Called once on mount; items are persisted across Livewire requests.
     */
    private function buildFormItems(): void
    {
        $organisationId = (int) (auth()->user()?->organisation_id ?? 0);

        $form = app(QuoteFormBuilder::class)->build(
            moduleSlug: $this->moduleSlug,
            organisationId: $organisationId,
        );

        $this->formItems = $form['items'];

        foreach ($this->formItems as $item) {
            $key = (string) ($item['key'] ?? '');
            if ($key === '') continue;

            if (! isset($this->itemInputs[$key])) {
                $isLabour  = strtolower((string) ($item['type'] ?? '')) === 'labour';
                $unitPrice = $isLabour
                    ? (float) $this->labourRate
                    : (float) ($item['unit_price'] ?? 0);

                $this->itemInputs[$key] = [
                    'price'    => $unitPrice > 0 ? $unitPrice : 0,
                    'quantity' => 0,
                    'enabled'  => ! (bool) ($item['enabled'] === false || ($item['is_optional'] ?? false)),
                ];
            }
        }

        $this->syncAutoResolvedItemPrices();
    }

    private function applyDefaultFormValues(): void
    {
        $organisationId = (int) (auth()->user()?->organisation_id ?? 0);

        $global = config('quotes.form_defaults.global', []);
        $module = config('quotes.form_defaults.modules.'.$this->moduleSlug, []);

        $organisationDefaults = $this->loadOrganisationFormDefaults($organisationId);
        $organisationGlobal   = is_array($organisationDefaults['global'] ?? null)
            ? $organisationDefaults['global'] : [];
        $organisationModule   = is_array($organisationDefaults['modules'][$this->moduleSlug] ?? null)
            ? $organisationDefaults['modules'][$this->moduleSlug] : [];

        // Precedence: organisation module > organisation global > config module > config global
        $defaults = array_merge($global, $module, $organisationGlobal, $organisationModule);

        $this->length      = (string) ($defaults['length'] ?? 10);
        $this->height      = $this->normalizeHeightValue($defaults['height'] ?? 1.8);
        $labourRateRaw     = (int) ($defaults['labour_rate'] ?? 0);
        $this->labourRate  = (string) ($labourRateRaw > 0 ? $labourRateRaw : 35);
        $this->markup      = (string) ($defaults['markup'] ?? 15);
        $this->waste       = (string) ($defaults['waste'] ?? 8);
        $this->vatRate     = (string) ($defaults['vat_rate'] ?? 20);
        $this->paymentTerms = (string) ($defaults['payment_terms'] ?? '');
        $this->useMarkup   = (bool) ($defaults['use_markup'] ?? false);
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

    public function updatedHeight(): void
    {
        $this->height = $this->normalizeHeightValue($this->height);
        $this->syncAutoResolvedItemPrices();
    }

    public function updatedUseMarkup(): void
    {
        $this->syncAutoResolvedItemPrices();
    }

    public function updatedLabourRate(): void
    {
        foreach ($this->formItems as $item) {
            $key = (string) ($item['key'] ?? '');
            if ($key !== '' && strtolower((string) ($item['type'] ?? '')) === 'labour') {
                $this->itemInputs[$key]['price'] = $this->labourRate;
            }
        }
    }

    public function saveQuote(): void
    {
        $this->customerPhone = $this->normalizeUkMobile($this->customerPhone);
        try {
            $this->validate($this->rules());
        } catch (ValidationException $exception) {
            $this->dispatch('quote-save-validation-failed');
            throw $exception;
        }

        $user      = auth()->user();
        $breakdown = $this->getBreakdown((int) ($user->organisation_id ?? 0));

        $quote     = $this->createQuoteRecord($breakdown, (int) $user->id, (int) ($user->organisation_id ?? 0));
        $pdfBytes  = $this->renderPdfBytes($quote, $breakdown, (int) $user->id, (string) $user->email, (string) $user->name);
        $this->persistQuotePdf($quote, $pdfBytes);

        $this->dispatch('toast', message: 'Quote saved successfully. PDF snapshot attached.', type: 'success');
        // redirect to the quote edit page to show the saved quote with the generated PDF and allow further edits
        redirect()->route('quotes.edit', ['quote' => $quote->id]);
    }

    public function saveTemplate(): void
    {
        $this->customerPhone = $this->normalizeUkMobile($this->customerPhone);
        $this->validate($this->rules());

        $user      = auth()->user();
        $breakdown = $this->getBreakdown((int) ($user->organisation_id ?? 0));

        $module = Modules::query()->firstOrCreate(
            ['slug' => $this->moduleSlug],
            ['name' => Str::title($this->moduleSlug)]
        );

        $normalizedGates = $this->normalizeGates($this->gates);
        $templateGateWidth = $this->resolveTemplateGateWidth($normalizedGates, $this->gateWidth);

        QuoteTemplates::query()->create([
            'organisation_id' => (int) ($user->organisation_id ?? 0),
            'created_by'      => (int) $user->id,
            'name'            => $this->jobName !== '' ? $this->jobName : $this->defaultJobName(),
            'module_id'       => (int) $module->id,
            'variant_key'     => $this->moduleSlug,
            'data'            => [
                'customer_name' => $this->customerName,
                'customer_phone' => $this->customerPhone,
                'job_name'      => $this->jobName,
                'length'        => $breakdown['length'],
                'height'        => $this->toFloat($this->normalizeHeightValue($this->height)),
                'gate_width'    => $templateGateWidth,
                'gates'         => $normalizedGates,
                'labour_rate'   => $breakdown['labour_rate'],
                'labour_total_override' => $this->normalizedLabourTotalOverride(),
                'markup'        => $breakdown['markup'],
                'waste'         => $breakdown['waste'],
                'vat_rate'      => $breakdown['vat_rate'],
                'payment_terms' => $this->paymentTerms,
                'item_quantity_overrides' => $this->templateQuantityOverrides(),
            ],
        ]);

        $this->dispatch('toast', message: 'Template saved successfully.', type: 'success');
    }

    public function prepareWhatsApp(): void
    {
        $this->customerPhone = $this->normalizeUkMobile($this->customerPhone);
        $this->validate($this->rules());

        $user      = auth()->user();
        $breakdown = $this->getBreakdown((int) ($user->organisation_id ?? 0));
        $quote     = $this->createQuoteRecord($breakdown, (int) $user->id, (int) ($user->organisation_id ?? 0));

        $customerName = trim($this->customerName) !== '' ? trim($this->customerName) : 'there';
        $total        = '£'.number_format($breakdown['total'], 2);
        $quoteLink    = route('quote.public', ['uuid' => $quote->uuid]);

        $this->whatsappMessage = "Hi {$customerName},\n\nHere's your quote for the fencing work:\n\nTotal: {$total}\n\nYou can view the full breakdown here:\n{$quoteLink}\n\nLet me know if you'd like to go ahead 👍";
        $this->whatsappPhone = $this->toWhatsAppPhone($this->customerPhone);

        $this->dispatch('open-whatsapp-modal');
    }

    public function downloadPdf()
    {
        $this->customerPhone = $this->normalizeUkMobile($this->customerPhone);
        $this->validate($this->rules());

        $user      = auth()->user();
        $breakdown = $this->getBreakdown((int) ($user->organisation_id ?? 0));
        $quote     = $this->createQuoteRecord($breakdown, (int) $user->id, (int) ($user->organisation_id ?? 0));
        $pdfBytes  = $this->renderPdfBytes($quote, $breakdown, (int) $user->id, (string) $user->email, (string) $user->name);
        $this->persistQuotePdf($quote, $pdfBytes);

        $fileName = 'tradepulse-quote-'.$quote->id.'.pdf';

        return response()->streamDownload(function () use ($pdfBytes): void {
            echo $pdfBytes;
        }, $fileName, ['Content-Type' => 'application/pdf']);
    }

    public function render(): \Illuminate\View\View
    {
        $breakdown = $this->getBreakdown((int) (auth()->user()?->organisation_id ?? 0));

        return view('livewire.fence-quote-builder', [
            'breakdown' => $breakdown,
        ]);
    }

    private function createQuoteRecord(array $breakdown, int $userId, int $organisationId): Quotes
    {
        $module = Modules::query()->firstOrCreate(
            ['slug' => $this->moduleSlug],
            ['name' => Str::title($this->moduleSlug)]
        );

        $breakdownForStorage = $breakdown;
        $normalizedGates = $this->normalizeGates($this->gates);
        $breakdownForStorage['height'] = $this->toFloat($this->normalizeHeightValue($this->height));
        $breakdownForStorage['gate_width'] = $this->resolveTemplateGateWidth($normalizedGates, $this->gateWidth);
        $breakdownForStorage['gates'] = $normalizedGates;
        $breakdownForStorage['labour_total_override'] = $this->normalizedLabourTotalOverride();
        $breakdownForStorage['item_quantity_overrides'] = $this->templateQuantityOverrides();

        return DB::transaction(function () use ($breakdown, $breakdownForStorage, $organisationId, $userId, $module): Quotes {
            $quote = Quotes::query()->create([
                'organisation_id' => $organisationId,
                'created_by'      => $userId,
                'module_id'       => (int) $module->id,
                'variant_key'     => Str::slug($this->jobName !== '' ? $this->jobName : $this->defaultJobName()),
                'customer_name'   => trim($this->customerName),
                'customer_phone'  => $this->customerPhone,
                'job_name'        => $this->jobName !== '' ? $this->jobName : $this->defaultJobName(),
                'status'          => 'draft',
                'length'          => $breakdown['length'],
                'labour_type'     => 'per_metre',
                'labour_total'    => (int) round($breakdown['labour_total'] * 100),
                'materials_total' => (int) round($breakdown['materials_with_waste'] * 100),
                'subtotal_price'  => (int) round($breakdown['subtotal_with_markup'] * 100),
                'vat_rate'        => $breakdown['vat_rate'],
                'vat_total'       => (int) round($breakdown['vat'] * 100),
                'payment_terms'   => trim($this->paymentTerms) !== '' ? trim($this->paymentTerms) : null,
                'calculation_data' => $breakdownForStorage,
                'total_price'     => (int) round($breakdown['total'] * 100),
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
                'quote_id'       => (int) $quote->id,
                'module_item_id' => isset($item['module_item_id']) ? (int) $item['module_item_id'] : null,
                'name'           => (string) ($item['name'] ?? 'Quote item'),
                'quantity'       => (int) round((float) ($item['quantity'] ?? 0)),
                'unit_price'     => (int) round((float) ($item['unit_price'] ?? 0) * 100),
                'total_price'    => (int) round((float) ($item['total'] ?? 0) * 100),
                'created_at'     => now(),
                'updated_at'     => now(),
            ];
        }

        QuoteItems::query()->insert($rows);
    }

    private function persistQuotePdf(Quotes $quote, string $pdfBytes): void
    {
        $path = 'quotes/'.$quote->id.'/quote-'.now()->format('Ymd-His').'.pdf';

        Storage::disk('public')->put($path, $pdfBytes);

        $quote->forceFill([
            'pdf_path'         => $path,
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
            $businessName = trim($fallbackBusinessName) !== '' ? trim($fallbackBusinessName) : 'Quote Customer';
        }

        $contactPhone  = trim((string) ($organisation?->phone ?? ''));
        $contactEmail  = $fallbackEmail;
        $logoPath      = trim((string) ($organisation?->logo ?? ''));
        $logoDataUri   = null;

        if ($logoPath !== '') {
            $absolutePath = public_path(ltrim($logoPath, '/'));
            if (is_file($absolutePath) && is_readable($absolutePath)) {
                $extension = strtolower((string) pathinfo($absolutePath, PATHINFO_EXTENSION));
                $mimeType  = match ($extension) {
                    'png'        => 'image/png',
                    'jpg', 'jpeg' => 'image/jpeg',
                    'gif'        => 'image/gif',
                    default      => null,
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
            'businessName'   => $businessName,
            'contactPhone'   => $contactPhone,
            'contactEmail'   => $contactEmail,
            'logoDataUri'    => $logoDataUri,
            'customerName'   => trim((string) ($quote->customer_name ?? '')),
            'jobDescription' => trim((string) ($quote->job_name ?? '')) !== ''
                ? trim((string) $quote->job_name)
                : 'Supply and install fencing - '.rtrim(rtrim(number_format($breakdown['length'], 2, '.', ''), '0'), '.').'m',
            'breakdown'      => $breakdown,
            'paymentTerms'   => trim((string) ($quote->payment_terms ?? '')),
            'isPro'          => $isPro,
            'quoteDate'      => $quote->created_at ?? now(),
            'validUntil'     => ($quote->created_at ?? now())->copy()->addDays(14),
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

        $this->jobName      = (string) ($data['job_name'] ?? $template->name ?? $this->defaultJobName());
        $this->customerName = (string) ($data['customer_name'] ?? '');
        $this->customerPhone = $this->normalizeUkMobile((string) ($data['customer_phone'] ?? ''));
        $this->length       = (string) ($data['length'] ?? $this->length);
        $this->height       = $this->normalizeHeightValue($data['height'] ?? $this->height);

        $templateGates = $this->normalizeGates(is_array($data['gates'] ?? null) ? $data['gates'] : []);
        $templateGateWidth = $this->toFloat($data['gate_width'] ?? 0);

        if ($templateGates === [] && $templateGateWidth > 0) {
            $templateGates[] = [
                'width' => $templateGateWidth,
                'price' => $this->defaultGatePrice(),
            ];
        }

        $this->gates = $templateGates;
        $this->gateWidth = (string) $this->resolveTemplateGateWidth($templateGates, $templateGateWidth);
        $this->labourRate   = (string) ($data['labour_rate'] ?? $this->labourRate);
        $this->labourTotalOverride = isset($data['labour_total_override']) && $data['labour_total_override'] !== null
            ? (string) $data['labour_total_override']
            : '';
        $this->markup       = (string) ($data['markup'] ?? $this->markup);
        $this->waste        = (string) ($data['waste'] ?? $this->waste);
        $this->vatRate      = (string) ($data['vat_rate'] ?? $this->vatRate);
        $this->paymentTerms = (string) ($data['payment_terms'] ?? $this->paymentTerms);
        $this->applyTemplateQuantityOverrides($data['item_quantity_overrides'] ?? []);

        $this->syncAutoResolvedItemPrices();

        $this->dispatch('toast', message: 'Template loaded. Tweak values and save your quote.', type: 'info');
    }

    private function defaultJobName(): string
    {
        return 'Fence job - '.rtrim(rtrim(number_format($this->toFloat($this->length), 2, '.', ''), '0'), '.').'m';
    }

    private function rules(): array
    {
        return [
            'customerName' => ['required', 'string', 'max:150'],
            'customerPhone' => ['required', 'string', 'regex:/^(?:\\+447\\d{9}|07\\d{9})$/'],
            'jobName'      => ['nullable', 'string', 'max:150'],
            'length'       => ['required', 'numeric', 'min:0.1', 'max:10000'],
            'height'       => ['required', 'in:1.5,1.8,2.0'],
            'gateWidth'    => ['nullable', 'numeric', 'min:0', 'max:100'],
            'labourRate'   => ['required', 'numeric', 'min:0', 'max:10000'],
            'labourTotalOverride' => ['nullable', 'numeric', 'min:0', 'max:1000000'],
            'markup'       => [$this->moduleSlug !== 'fencing' || $this->useMarkup ? 'required' : 'nullable', 'numeric', 'min:0', 'max:200'],
            'waste'        => ['required', 'numeric', 'min:0', 'max:100'],
            'vatRate'      => ['required', 'numeric', 'min:0', 'max:100'],
            'paymentTerms' => ['nullable', 'string', 'max:1000'],
        ];
    }

    private function getBreakdown(int $organisationId): array
    {
        // Disable whichever covering type the user has not selected
        $excludedKey = $this->fencingType === 'panels' ? 'boards' : 'panels';

        $items = array_map(function (array $item) use ($excludedKey): array {
            $key = (string) ($item['key'] ?? '');

            if ($key === $excludedKey) {
                $item['enabled'] = false;
                return $item;
            }

            if (isset($this->itemInputs[$key]['enabled'])) {
                $item['enabled'] = (bool) $this->itemInputs[$key]['enabled'];
            }

            return $item;
        }, $this->formItems);

        return app(QuoteService::class)->calculate([
            'organisation_id' => $organisationId,
            'module_slug'     => $this->moduleSlug,
            'fencing_type'    => $this->fencingType,
            'height'          => $this->toFloat($this->height),
            'gate_width'      => $this->toFloat($this->gateWidth),
            'gates'           => $this->gates,
            'inputs'          => [
                'length'      => $this->toFloat($this->length),
                'labour_rate' => $this->toFloat($this->labourRate),
                'labour_total_override' => $this->normalizedLabourTotalOverride(),
                'markup'      => ($this->moduleSlug !== 'fencing' || $this->useMarkup) ? $this->toFloat($this->markup) : 0,
                'waste'       => $this->toFloat($this->waste),
                'vat_rate'    => $this->toFloat($this->vatRate),
            ],
            'use_markup' => $this->useMarkup,
            'items'       => $items,
            'item_inputs' => $this->itemInputs,
        ]);
    }

    private function syncAutoResolvedItemPrices(): void
    {
        foreach ($this->formItems as $item) {
            if (! $this->isFenceHeightPricedItem($item)) {
                continue;
            }

            $key = (string) ($item['key'] ?? '');
            if ($key === '') {
                continue;
            }

            $resolvedPrice = $this->resolveItemPriceForCurrentHeight($item);
            $currentRaw = $this->itemInputs[$key]['price'] ?? '';
            $currentPrice = is_numeric($currentRaw) ? (float) $currentRaw : 0.0;
            $previousAutoPrice = (float) ($this->autoResolvedItemPrices[$key] ?? 0.0);

            if ($currentRaw === '' || abs($currentPrice - $previousAutoPrice) <= 0.0001) {
                $this->itemInputs[$key]['price'] = $resolvedPrice > 0
                    ? rtrim(rtrim(number_format($resolvedPrice, 2, '.', ''), '0'), '.')
                    : '';
            }

            $this->autoResolvedItemPrices[$key] = $resolvedPrice;
        }
    }

    private function isFenceHeightPricedItem(array $item): bool
    {
        return $this->moduleSlug === 'fencing'
            && strtolower((string) ($item['type'] ?? 'material')) === 'material'
            && (string) ($item['key'] ?? '') !== 'gate';
    }

    private function resolveItemPriceForCurrentHeight(array $item): float
    {
        $prices = $this->useMarkup
            ? ($item['cost_prices_by_height'] ?? [])
            : ($item['sell_prices_by_height'] ?? []);

        $resolved = $this->resolvePriceFromHeightMap($prices, $this->toFloat($this->height));

        if ($resolved > 0) {
            return $resolved;
        }

        $metaPrices = is_array($item['meta']['prices'] ?? null) ? $item['meta']['prices'] : [];

        $resolved = $this->resolvePriceFromHeightMap($metaPrices, $this->toFloat($this->height));

        if ($resolved > 0) {
            return $resolved;
        }

        return max(0.0, (float) ($item['unit_price'] ?? 0));
    }

    private function resolvePriceFromHeightMap(mixed $priceMap, float $height): float
    {
        if (! is_array($priceMap) || $priceMap === []) {
            return 0.0;
        }

        foreach ($priceMap as $configuredHeight => $configuredPrice) {
            if (abs((float) $configuredHeight - $height) < 0.0001) {
                return max(0.0, (float) $configuredPrice);
            }
        }

        $closestHigherHeight = collect($priceMap)
            ->keys()
            ->map(fn ($configuredHeight) => (string) $configuredHeight)
            ->sortBy(fn (string $configuredHeight) => (float) $configuredHeight)
            ->first(fn (string $configuredHeight) => (float) $configuredHeight >= $height);

        if ($closestHigherHeight !== null && isset($priceMap[$closestHigherHeight])) {
            return max(0.0, (float) $priceMap[$closestHigherHeight]);
        }

        return 0.0;
    }

    private function toFloat(string|int|float|null $value): float
    {
        return max(0.0, (float) $value);
    }

    private function normalizeUkMobile(string|int|float|null $value): string
    {
        $phone = preg_replace('/[^\\d\\+]/', '', trim((string) $value)) ?? '';

        if (str_starts_with($phone, '00447')) {
            return '+447'.substr($phone, 5);
        }

        if (str_starts_with($phone, '447')) {
            return '+'.$phone;
        }

        return $phone;
    }

    private function toWhatsAppPhone(string $phone): string
    {
        if (str_starts_with($phone, '07')) {
            return '+44'.substr($phone, 1);
        }

        return $phone;
    }

    private function normalizeHeightValue(string|int|float|null $height): string
    {
        $candidate = (float) $height;

        if ($candidate <= 0) {
            $candidate = 1.8;
        }

        return number_format($this->nearestAllowedHeight($candidate), 1, '.', '');
    }

    private function nearestAllowedHeight(float $height): float
    {
        $closest = self::ALLOWED_HEIGHTS[0];
        $closestDistance = INF;

        foreach (self::ALLOWED_HEIGHTS as $allowedHeight) {
            $distance = abs($allowedHeight - $height);

            if ($distance < $closestDistance - 0.0001 || (abs($distance - $closestDistance) <= 0.0001 && $allowedHeight > $closest)) {
                $closest = $allowedHeight;
                $closestDistance = $distance;
            }
        }

        return $closest;
    }

    private function normalizeGates(array $gates): array
    {
        $normalized = [];

        foreach ($gates as $gate) {
            if (! is_array($gate)) {
                continue;
            }

            $normalized[] = [
                'width' => max(0.0, (float) ($gate['width'] ?? 0)),
                'price' => max(0.0, (float) ($gate['price'] ?? 0)),
            ];
        }

        return $normalized;
    }

    private function resolveTemplateGateWidth(array $gates, string|int|float|null $fallbackWidth = null): float
    {
        if ($gates !== []) {
            return array_reduce($gates, function (float $carry, array $gate): float {
                return $carry + max(0.0, (float) ($gate['width'] ?? 0));
            }, 0.0);
        }

        return $this->toFloat($fallbackWidth);
    }

    private function defaultGatePrice(): float
    {
        return max(0.0, (float) ($this->itemInputs['gate']['price'] ?? 0));
    }

    private function normalizedLabourTotalOverride(): ?float
    {
        if ($this->labourTotalOverride === '' || $this->labourTotalOverride === null) {
            return null;
        }

        if (! is_numeric($this->labourTotalOverride)) {
            return null;
        }

        return round(max(0.0, (float) $this->labourTotalOverride), 2);
    }

    private function templateQuantityOverrides(): array
    {
        $overrides = [];

        foreach ($this->formItems as $item) {
            $key = (string) ($item['key'] ?? '');
            $isFormulaMaterial = strtolower((string) ($item['type'] ?? '')) === 'material'
                && strtolower((string) ($item['calculation'] ?? '')) === 'formula';

            if ($key === '' || ! $isFormulaMaterial) {
                continue;
            }

            $qty = (int) ($this->itemInputs[$key]['quantity'] ?? 0);
            if ($qty > 0) {
                $overrides[$key] = $qty;
            }
        }

        return $overrides;
    }

    private function applyTemplateQuantityOverrides(mixed $overrides): void
    {
        if (! is_array($overrides)) {
            return;
        }

        foreach ($overrides as $key => $qty) {
            if (! isset($this->itemInputs[$key])) {
                continue;
            }

            $this->itemInputs[$key]['quantity'] = max(0, (int) $qty);
        }
    }

    private function isProUser(int $userId): bool
    {
        $activeStatuses = ['active', 'trialing', 'past_due'];

        return DB::table('subscriptions')
            ->where('user_id', $userId)
            ->whereIn('stripe_status', $activeStatuses)
            ->exists();
    }

    public function addGate()
    {
        $this->gates[] = [
            'width' => 1.0,
            'price' => isset($this->itemInputs['gate']['price']) ? $this->itemInputs['gate']['price'] : 0,
        ];
    }

    public function removeGate($index)
    {
        unset($this->gates[$index]);
        $this->gates = array_values($this->gates);
    }
}

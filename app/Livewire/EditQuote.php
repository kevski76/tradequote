<?php

namespace App\Livewire;

use App\Models\Modules;
use App\Models\Organisations;
use App\Models\QuoteItems;
use App\Models\QuoteTemplates;
use App\Models\Quotes;
use App\Services\QuoteService;
use App\Services\QuoteFormBuilder;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class EditQuote extends Component
{
    private const ALLOWED_HEIGHTS = [1.5, 1.8, 2.0];

    public int $quoteId;

    public string $moduleSlug = 'fencing';

    /**
     * For the fencing module: which covering type is active ('panels' or 'boards').
     * The inactive type is excluded from the Item Prices card and calculation.
     */
    public string $fencingType = 'panels';

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
     * quantity — quantity override; empty string = auto-calculated from formula
     *
     * @var array<string, array{price:string, quantity:string}>
     */
    public array $itemInputs = [];

    public array $autoResolvedItemPrices = [];

    public string $customerName = '';

    public string $customerPhone = '';

    public string $jobName = '';

    public string $length = '15';

    public string $height = '1.8';

    public string $labourRate = '35';

    public string $labourTotalOverride = '';

    public string $markup = '15';

    public string $waste = '8';

    public string $vatRate = '20';

    public string $gateWidth = '0';
    public array $gates = [];

    public string $paymentTerms = '';

    public bool $useMarkup = false;

    public string $whatsappPhone = '';

    public string $whatsappMessage = '';

    public string $status = 'draft';

    public function mount(int $quoteId): void
    {
        $this->quoteId = $quoteId;

        $user = auth()->user();
        $quote = Quotes::query()->findOrFail($quoteId);

        $ownedByUser = (int) $quote->created_by === (int) $user->id;
        $orgId = (int) ($user->organisation_id ?? 0);
        $ownedByOrg = $orgId > 0 && (int) $quote->organisation_id === $orgId;

        if (! $ownedByUser && ! $ownedByOrg) {
            throw new AccessDeniedHttpException();
        }

        $data = is_array($quote->calculation_data) ? $quote->calculation_data : [];

        // Choose a canonical length while preserving decimal precision for legacy data.
        $storedLength = (float) ($data['length'] ?? 0);
        $quoteLength  = (float) ($quote->length ?? 0);

        $storedHasDecimals = $storedLength > 0 && abs($storedLength - (int) $storedLength) > 0.0001;
        $quoteHasDecimals  = $quoteLength > 0 && abs($quoteLength - (int) $quoteLength) > 0.0001;

        $canonicalLength = match (true) {
            $storedHasDecimals && ! $quoteHasDecimals => $storedLength,
            $quoteHasDecimals && ! $storedHasDecimals => $quoteLength,
            default => $quoteLength > 0 ? $quoteLength : $storedLength,
        };

        // Heal both sources when mismatched so future loads stay consistent.
        if ($canonicalLength > 0 && (abs($storedLength - $canonicalLength) > 0.0001 || abs($quoteLength - $canonicalLength) > 0.0001)) {
            $data['length'] = $canonicalLength;
            $quote->forceFill([
                'length' => $canonicalLength,
                'calculation_data' => $data,
            ])->saveQuietly();
        }

        $this->moduleSlug = (string) ($quote->module?->slug ?? $quote->variant_key ?? $this->moduleSlug);

        $this->customerName = (string) ($quote->customer_name ?? '');
        $this->customerPhone = $this->normalizeUkMobile((string) ($quote->customer_phone ?? ''));
        $this->jobName = (string) ($quote->job_name ?? '');
        $this->length = (string) ($canonicalLength > 0 ? $canonicalLength : $this->length);
        $this->height = $this->normalizeHeightValue($data['height'] ?? $this->height);
        $savedLabourRate = (float) ($data['labour_rate'] ?? 0);
        $this->labourRate = $savedLabourRate > 0 ? (string) $savedLabourRate : $this->labourRate;
        $this->labourTotalOverride = isset($data['labour_total_override']) && $data['labour_total_override'] !== null
            ? (string) $data['labour_total_override']
            : '';
        $this->markup = (string) ($data['markup'] ?? $this->markup);
        $this->waste = (string) ($data['waste'] ?? $this->waste);
        $this->vatRate = (string) ($data['vat_rate'] ?? $quote->vat_rate ?? $this->vatRate);
        $this->paymentTerms = (string) ($quote->payment_terms ?? '');
        $this->gates = $this->normalizeGates(is_array($data['gates'] ?? null) ? $data['gates'] : []);
        $this->gateWidth = (string) ($data['gate_width'] ?? 0);
        $this->buildFormItems();
        $this->applyTemplateQuantityOverrides($data['item_quantity_overrides'] ?? []);

        // Restore previously saved gate price when the quote contains a gate item.
        $savedItems = is_array($data['items'] ?? null) ? $data['items'] : [];
        $defaultGatePrice = $this->firstGatePrice($this->gates);

        foreach ($savedItems as $savedItem) {
            if ((string) ($savedItem['key'] ?? '') !== 'gate') {
                continue;
            }

            $savedGatePrice = (float) ($savedItem['unit_price'] ?? $savedItem['total'] ?? 0);
            if ($defaultGatePrice === null && $savedGatePrice > 0) {
                // Legacy quotes may only have the aggregated gate line item.
                $defaultGatePrice = $savedGatePrice;
            }

            // Backfill gate width for older quotes that stored a gate item but no gate_width.
            if ((float) $this->gateWidth <= 0) {
                $savedGateName = (string) ($savedItem['name'] ?? '');
                if (preg_match('/\((\d+(?:\.\d+)?)\s*m\)/i', $savedGateName, $matches) === 1) {
                    $this->gateWidth = (string) $matches[1];
                } else {
                    // Legacy rows like "Fence Gate" have no embedded width; keep unknown as 0.
                    $this->gateWidth = '0';
                }
            }

            break;
        }

        if (count($this->gates) === 0 && (float) $this->gateWidth > 0) {
            $this->gates[] = [
                'width' => $this->toFloat($this->gateWidth),
                'price' => $defaultGatePrice ?? 0.0,
            ];
        }

        $this->gateWidth = (string) $this->resolveTemplateGateWidth($this->gates, $this->gateWidth);

        if ($defaultGatePrice !== null || count($this->gates) > 0 || (float) $this->gateWidth > 0) {
            $this->itemInputs['gate'] = [
                'price' => $defaultGatePrice !== null ? (string) $defaultGatePrice : '',
                'quantity' => '1',
                'enabled' => true,
            ];
        }

        $this->inferFencingType($data);

        $orgDefaults = $this->loadOrganisationFormDefaults($orgId);
        $orgGlobal = is_array($orgDefaults['global'] ?? null) ? $orgDefaults['global'] : [];
        $global = config('quotes.form_defaults.global', []);
        $this->whatsappPhone = (string) ($orgGlobal['whatsapp_phone'] ?? $global['whatsapp_phone'] ?? '+44');

        $configModule = (array) config('quotes.form_defaults.modules.'.$this->moduleSlug, []);
        $orgModule    = is_array($orgDefaults['modules'][$this->moduleSlug] ?? null)
            ? $orgDefaults['modules'][$this->moduleSlug] : [];
        $this->useMarkup = (bool) (array_merge($configModule, $orgModule)['use_markup'] ?? false);
        $this->syncAutoResolvedItemPrices();

        // Load quote status
        $this->status = $quote->normalizeStatus($quote->status ?? 'draft');
    }

    public function toggleStatus(string $newStatus): void
    {
        if (!in_array($newStatus, Quotes::$statuses, true)) {
            return;
        }

        $quote = Quotes::query()->findOrFail($this->quoteId);
        $oldStatus = $quote->normalizeStatus($quote->status ?? Quotes::STATUS_DRAFT);
        $quote->update(['status' => $newStatus]);
        $this->status = $newStatus;
        $this->dispatch('toast', message: 'Quote status updated to ' . ucfirst(str_replace('_', ' ', $newStatus)), type: 'success');

        if ($newStatus === Quotes::STATUS_WORK_COMPLETE && $oldStatus !== Quotes::STATUS_WORK_COMPLETE) {
            $this->prepareCompletionWhatsApp();
        }
    }

    /**
     * Load module items with organisation prices into $formItems.
     * Called once on mount. Items are persisted across Livewire requests
     * so prices are only read from the DB on initial page load.
     */
    private function buildFormItems(): void
    {
        $organisationId = (int) (auth()->user()?->organisation_id ?? 0);

        $form = app(QuoteFormBuilder::class)->build(
            moduleSlug: $this->moduleSlug,
            organisationId: $organisationId,
        );

        $this->formItems = $form['items'];

        // Initialise itemInputs from resolved org/DB prices; quantity empty = auto
        foreach ($this->formItems as $item) {
            $key = (string) ($item['key'] ?? '');
            if ($key === '') continue;

            // Only set defaults on mount (don't overwrite user edits after re-mount)
            if (! isset($this->itemInputs[$key])) {
                // Labour items use the dedicated labourRate field as their default price.
                // This avoids using the org DB price which may be stored in pence.
                $isLabour  = strtolower((string) ($item['type'] ?? '')) === 'labour';
                $unitPrice = $isLabour
                    ? (float) $this->labourRate
                    : (float) ($item['unit_price'] ?? 0);
                $this->itemInputs[$key] = [
                    // Show blank when price is 0 — user sees placeholder rather than "0"
                    'price'    => $unitPrice > 0 ? (string) $unitPrice : '',
                    'quantity' => '',
                    // Optional items start disabled; user must explicitly add them
                    'enabled'  => ! (bool) ($item['enabled'] === false || ($item['is_optional'] ?? false)),
                ];
            }
        }

        $this->syncAutoResolvedItemPrices();
    }

    /**
     * Infer which fencing covering type (panels vs boards) was used when the quote
     * was originally saved, so the toggle defaults to the correct option on edit.
     * Item prices are always taken from current org prices (via buildFormItems).
     */
    private function inferFencingType(array $data): void
    {
        if ($this->moduleSlug !== 'fencing') {
            return;
        }

        $savedItems = is_array($data['items'] ?? null) ? $data['items'] : [];
        if ($savedItems === []) {
            return;
        }

        $keys = array_column($savedItems, 'key');

        if (in_array('boards', $keys, true) && ! in_array('panels', $keys, true)) {
            $this->fencingType = 'boards';
        }
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

    public function saveQuoteWithInputs(string|int|float|null $length, string|int|float|null $gateWidth = null): void
    {
        if ($length !== null && $length !== '') {
            $this->length = (string) $length;
        }

        if ($gateWidth !== null && $gateWidth !== '') {
            $this->gateWidth = (string) $gateWidth;
        }

        $this->saveQuote();
    }

    public function saveQuote(): void
    {
        $this->customerPhone = $this->normalizeUkMobile($this->customerPhone);
        $this->validate($this->rules());

        $user = auth()->user();
        $quote = Quotes::query()->findOrFail($this->quoteId);
        $breakdown = $this->getBreakdown((int) $quote->organisation_id);

        // Persist gate state alongside breakdown so edit reloads are deterministic.
        $breakdownForStorage = $breakdown;
        $normalizedGates = $this->normalizeGates($this->gates);
        $breakdownForStorage['height'] = $this->toFloat($this->normalizeHeightValue($this->height));
        $breakdownForStorage['gate_width'] = $this->resolveTemplateGateWidth($normalizedGates, $this->gateWidth);
        $breakdownForStorage['gates'] = $normalizedGates;
        $breakdownForStorage['labour_total_override'] = $this->normalizedLabourTotalOverride();
        $breakdownForStorage['item_quantity_overrides'] = $this->templateQuantityOverrides();

        DB::transaction(function () use ($quote, $breakdown, $breakdownForStorage): void {
            $quote->forceFill([
                'customer_name' => trim($this->customerName),
                'customer_phone' => $this->customerPhone,
                'job_name' => $this->jobName !== '' ? $this->jobName : $this->defaultJobName(),
                'length' => $breakdown['length'],
                'labour_total' => (int) round($breakdown['labour_total'] * 100),
                'materials_total' => (int) round($breakdown['materials_with_waste'] * 100),
                'subtotal_price' => (int) round($breakdown['subtotal_with_markup'] * 100),
                'vat_rate' => $breakdown['vat_rate'],
                'vat_total' => (int) round($breakdown['vat'] * 100),
                'payment_terms' => trim($this->paymentTerms) !== '' ? trim($this->paymentTerms) : null,
                'calculation_data' => $breakdownForStorage,
                'total_price' => (int) round($breakdown['total'] * 100),
            ])->save();

            $this->storeQuoteItems($quote, $breakdown);
        });

        $pdfBytes = $this->renderPdfBytes($quote, $breakdown, (int) $user->id, (string) $user->email, (string) $user->name);
        $this->persistQuotePdf($quote, $pdfBytes);

        $this->dispatch('toast', message: 'Quote updated successfully. PDF snapshot regenerated.', type: 'success');
    }

    public function downloadPdf(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $this->customerPhone = $this->normalizeUkMobile($this->customerPhone);
        $this->validate($this->rules());

        $user = auth()->user();
        $quote = Quotes::query()->findOrFail($this->quoteId);
        $breakdown = $this->getBreakdown((int) $quote->organisation_id);
        $pdfBytes = $this->renderPdfBytes($quote, $breakdown, (int) $user->id, (string) $user->email, (string) $user->name);

        $fileName = 'tradepulse-quote-'.$quote->id.'.pdf';

        return response()->streamDownload(function () use ($pdfBytes): void {
            echo $pdfBytes;
        }, $fileName, ['Content-Type' => 'application/pdf']);
    }

    public function saveTemplate(): void
    {
        $this->customerPhone = $this->normalizeUkMobile($this->customerPhone);
        $this->validate($this->rules());

        $user = auth()->user();
        $breakdown = $this->getBreakdown((int) ($user->organisation_id ?? 0));

        $module = Modules::query()->firstOrCreate(
            ['slug' => $this->moduleSlug],
            ['name' => Str::title($this->moduleSlug)]
        );

        $normalizedGates = $this->normalizeGates($this->gates);
        $templateGateWidth = $this->resolveTemplateGateWidth($normalizedGates, $this->gateWidth);

        QuoteTemplates::query()->create([
            'organisation_id' => (int) ($user->organisation_id ?? 0),
            'created_by' => (int) $user->id,
            'name' => $this->jobName !== '' ? $this->jobName : $this->defaultJobName(),
            'module_id' => (int) $module->id,
            'variant_key' => $this->moduleSlug,
            'data' => [
                'customer_name' => $this->customerName,
                'customer_phone' => $this->customerPhone,
                'job_name' => $this->jobName,
                'length' => $breakdown['length'],
                'height' => $this->toFloat($this->normalizeHeightValue($this->height)),
                'gate_width' => $templateGateWidth,
                'gates' => $normalizedGates,
                'labour_rate' => $breakdown['labour_rate'],
                'labour_total_override' => $this->normalizedLabourTotalOverride(),
                'markup' => $breakdown['markup'],
                'waste' => $breakdown['waste'],
                'vat_rate' => $breakdown['vat_rate'],
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

        $quote = Quotes::query()->findOrFail($this->quoteId);
        $breakdown = $this->getBreakdown((int) $quote->organisation_id);

        $customerName = trim($this->customerName) !== '' ? trim($this->customerName) : 'there';
        $total = '£'.number_format($breakdown['total'], 2);
        $quoteLink = route('quote.public', ['uuid' => $quote->uuid]);

        $this->whatsappMessage = "Hi {$customerName},\n\nHere's your quote for the fencing work:\n\nTotal: {$total}\n\nYou can view the full breakdown here:\n{$quoteLink}\n\nLet me know if you'd like to go ahead 👍";
        $this->whatsappPhone = $this->toWhatsAppPhone($this->customerPhone);

        $this->dispatch('open-whatsapp-modal');
    }

    public function prepareCompletionWhatsApp(): void
    {
        $this->customerPhone = $this->normalizeUkMobile($this->customerPhone);
        $this->validateOnly('customerName', $this->rules());
        $this->validateOnly('customerPhone', $this->rules());

        $quote = Quotes::query()->findOrFail($this->quoteId);
        $reviewLink = route('review.public', ['uuid' => $quote->uuid]);

        $customerName = trim($this->customerName) !== '' ? trim($this->customerName) : 'there';
        $this->whatsappPhone = $this->toWhatsAppPhone($this->customerPhone);
        $this->whatsappMessage = "Hi {$customerName},\n\nThank you for choosing us for your fencing work. We hope you're happy with everything we've completed.\n\nIf you have a moment, we'd really appreciate your feedback:\n{$reviewLink}\n\nThanks again!";

        $this->dispatch('open-whatsapp-modal');
    }

    public function render(): \Illuminate\View\View
    {
        $quote = Quotes::query()->find($this->quoteId);

        return view('livewire.edit-quote', [
            'breakdown' => $this->getBreakdown((int) ($quote?->organisation_id ?? 0)),
        ]);
    }

    private function storeQuoteItems(Quotes $quote, array $breakdown): void
    {
        $items = is_array($breakdown['items'] ?? null) ? $breakdown['items'] : [];

        QuoteItems::query()->where('quote_id', (int) $quote->id)->delete();

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
                'unit_price' => (int) round((float) ($item['unit_price'] ?? 0) * 100),
                'total_price' => (int) round((float) ($item['total'] ?? 0) * 100),
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

    private function defaultJobName(): string
    {
        return 'Fence job - '.rtrim(rtrim(number_format($this->toFloat($this->length), 2, '.', ''), '0'), '.').'m';
    }

    private function rules(): array
    {
        return [
            'customerName' => ['required', 'string', 'max:150'],
            'customerPhone' => ['required', 'string', 'regex:/^(?:\\+447\\d{9}|07\\d{9})$/'],
            'jobName' => ['nullable', 'string', 'max:150'],
            'length' => ['required', 'numeric', 'min:0.1', 'max:10000'],
            'height' => ['required', 'in:1.5,1.8,2.0'],
            'gateWidth' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'labourRate' => ['required', 'numeric', 'min:0', 'max:10000'],
            'labourTotalOverride' => ['nullable', 'numeric', 'min:0', 'max:1000000'],
            'markup' => [$this->moduleSlug !== 'fencing' || $this->useMarkup ? 'required' : 'nullable', 'numeric', 'min:0', 'max:200'],
            'waste' => ['required', 'numeric', 'min:0', 'max:100'],
            'vatRate' => ['required', 'numeric', 'min:0', 'max:100'],
            'paymentTerms' => ['nullable', 'string', 'max:1000'],
        ];
    }

    private function getBreakdown(int $organisationId): array
    {
        // For the fencing module, disable whichever covering type the user has not selected.
        $excludedKey = $this->moduleSlug === 'fencing'
            ? ($this->fencingType === 'panels' ? 'boards' : 'panels')
            : null;

        $items = array_map(function (array $item) use ($excludedKey): array {
            $key = (string) ($item['key'] ?? '');

            // Disable the excluded fencing type (panels vs boards toggle)
            if ($excludedKey !== null && $key === $excludedKey) {
                $item['enabled'] = false;
                return $item;
            }

            // Apply the user-controlled enabled state from itemInputs
            // (covers optional items the user has not yet added)
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
        return max(0, (float) $value);
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

    private function resolveTemplateGateWidth(array $gates, string|int|float|null $fallbackWidth = null): float
    {
        if ($gates !== []) {
            return array_reduce($gates, function (float $carry, array $gate): float {
                return $carry + max(0.0, (float) ($gate['width'] ?? 0));
            }, 0.0);
        }

        return $this->toFloat($fallbackWidth);
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

    private function firstGatePrice(array $gates): ?float
    {
        foreach ($gates as $gate) {
            $price = max(0.0, (float) ($gate['price'] ?? 0));

            if ($price > 0) {
                return $price;
            }
        }

        return null;
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
        $defaultGatePrice = isset($this->gates[0]['price'])
            ? (float) $this->gates[0]['price']
            : (float) ($this->itemInputs['gate']['price'] ?? 0);

        $this->gates[] = [
            'width' => 1.0,
            'price' => $defaultGatePrice > 0 ? $defaultGatePrice : 0,
        ];
    }

    public function removeGate($index)
    {
        unset($this->gates[$index]);
        $this->gates = array_values($this->gates);
    }
}

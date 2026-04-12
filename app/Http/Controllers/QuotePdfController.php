<?php

namespace App\Http\Controllers;

use App\Models\Organisations;
use App\Models\Quotes;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class QuotePdfController extends Controller
{
    public function show(Quotes $quote): StreamedResponse
    {
        $user = auth()->user();

        $isOwner = (int) $quote->created_by === (int) $user->id;
        $sameOrganisation = (int) ($user->organisation_id ?? 0) > 0
            && (int) $quote->organisation_id === (int) $user->organisation_id;

        abort_unless($isOwner || $sameOrganisation, 403);

        $disk = Storage::disk('public');

        if (! empty($quote->pdf_path) && $disk->exists($quote->pdf_path)) {
            return response()->streamDownload(function () use ($disk, $quote): void {
                $stream = $disk->readStream((string) $quote->pdf_path);

                if ($stream === false) {
                    return;
                }

                fpassthru($stream);
                fclose($stream);
            }, $this->fileName($quote->id), ['Content-Type' => 'application/pdf']);
        }

        $breakdown = $this->breakdownFromQuote($quote);
        $pdfBytes = $this->renderPdfBytes($user->id, $user->email, $user->name, $quote, $breakdown);

        $path = 'quotes/'.$quote->id.'/quote-'.now()->format('Ymd-His').'.pdf';
        $disk->put($path, $pdfBytes);

        $quote->forceFill([
            'pdf_path' => $path,
            'pdf_generated_at' => now(),
        ])->save();

        return response()->streamDownload(function () use ($pdfBytes): void {
            echo $pdfBytes;
        }, $this->fileName($quote->id), ['Content-Type' => 'application/pdf']);
    }

    private function fileName(int $quoteId): string
    {
        return 'tradepulse-quote-'.$quoteId.'.pdf';
    }

    private function breakdownFromQuote(Quotes $quote): array
    {
        $stored = is_array($quote->calculation_data) ? $quote->calculation_data : [];

        // Support both the new canonical key names and legacy keys stored in old quotes.
        $labourTotal        = (float) ($stored['labour_total'] ?? $stored['labour_cost'] ?? $quote->labour_total ?? 0);
        $materialsWithWaste = (float) ($stored['materials_with_waste'] ?? $stored['materials_cost'] ?? $stored['materials_total'] ?? $quote->materials_total ?? 0);
        $subtotalWithMarkup = (float) ($stored['subtotal_with_markup'] ?? $stored['subtotal'] ?? $quote->subtotal_price ?? ($labourTotal + $materialsWithWaste));
        $vatRate            = (float) ($stored['vat_rate'] ?? $quote->vat_rate ?? 0);
        $vat                = (float) ($stored['vat'] ?? $stored['vat_amount'] ?? $quote->vat_total ?? 0);
        $total              = (float) ($stored['total'] ?? $stored['total_price'] ?? $quote->total_price ?? ($subtotalWithMarkup + $vat));

        // Use stored items when available (new quotes); build legacy rows for old quotes
        $items = [];
        if (! empty($stored['items']) && is_array($stored['items'])) {
            $items = $stored['items'];
        } else {
            // Backwards compat: reconstruct item rows from legacy flat keys
            $postsQty    = (int) ($stored['posts_qty'] ?? 0);
            $postsPrice  = (float) ($stored['posts_price'] ?? 0);
            $boardsQty   = (int) ($stored['boards_qty'] ?? 0);
            $boardsPrice = (float) ($stored['boards_price'] ?? 0);

            if ($postsQty > 0) {
                $items[] = ['name' => 'Fence Posts', 'type' => 'material', 'quantity' => $postsQty, 'unit_price' => 0.0, 'total' => $postsPrice];
            }
            if ($boardsQty > 0) {
                $items[] = ['name' => 'Boards / Panels', 'type' => 'material', 'quantity' => $boardsQty, 'unit_price' => 0.0, 'total' => $boardsPrice];
            }
            if ($labourTotal > 0) {
                $length  = (float) ($stored['length'] ?? $quote->length ?? 0);
                $items[] = ['name' => 'Labour', 'type' => 'labour', 'quantity' => $length, 'unit_price' => 0.0, 'total' => $labourTotal];
            }
        }

        return [
            'items'               => $items,
            'length'              => (float) ($stored['length'] ?? $quote->length ?? 0),
            'materials_with_waste' => $materialsWithWaste,
            'labour_total'        => $labourTotal,
            'subtotal_with_markup' => $subtotalWithMarkup,
            'markup_amount'       => (float) ($stored['markup_amount'] ?? 0),
            'waste_amount'        => (float) ($stored['waste_amount'] ?? 0),
            'vat_rate'            => $vatRate,
            'vat'                 => $vat,
            'total'               => $total,
            'markup'              => (float) ($stored['markup'] ?? 0),
            'waste'               => (float) ($stored['waste'] ?? 0),
            'labour_rate'         => (float) ($stored['labour_rate'] ?? 0),
        ];
    }

    private function renderPdfBytes(int $userId, string $fallbackEmail, string $fallbackBusinessName, Quotes $quote, array $breakdown): string
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

        $isPro = DB::table('subscriptions')
            ->where('user_id', $userId)
            ->whereIn('stripe_status', ['active', 'trialing', 'past_due'])
            ->exists();

        $html = view('pdf.quote', [
            'businessName' => $businessName,
            'contactPhone' => $contactPhone,
            'contactEmail' => $contactEmail,
            'logoDataUri' => $logoDataUri,
            'customerName' => trim((string) ($quote->customer_name ?? '')),
            'jobDescription' => trim((string) ($quote->job_name ?? '')) !== ''
                ? trim((string) $quote->job_name)
                : Str::title(str_replace(['_', '-'], ' ', (string) ($quote->variant_key ?? 'Quote'))),
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
}

<?php

namespace App\Livewire;

use App\Models\Organisations;
use App\Models\QuoteTemplates;
use App\Models\Quotes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Livewire\Component;

class DashboardOverview extends Component
{
    public ?string $notice = null;

    public function createQuote(): void
    {
        redirect()->route('quotes.create');
    }

    public function useTemplate(int $templateId): void
    {
        redirect()->route('quotes.create', ['template' => $templateId]);
    }

    public function openQuote(int $quoteId): void
    {
        redirect()->route('quotes.edit', ['quote' => $quoteId]);
    }

    public function render(): \Illuminate\View\View
    {
        $user = auth()->user();

        $quotesQuery = Quotes::query()
            ->where('created_by', (int) $user->id)
            ->when((int) ($user->organisation_id ?? 0) > 0, function ($query) use ($user) {
                $query->where('organisation_id', (int) $user->organisation_id);
            });

        $quotesThisWeek = (clone $quotesQuery)
            ->where('created_at', '>=', now()->startOfWeek())
            ->count();

        $totalValueQuoted = (clone $quotesQuery)->sum('total_price') / 100;

        $hasStatusColumn = Schema::hasColumn('quotes', 'status');
        $hasJobNameColumn = Schema::hasColumn('quotes', 'job_name');
        $hasCustomerNameColumn = Schema::hasColumn('quotes', 'customer_name');
        $hasPdfPathColumn = Schema::hasColumn('quotes', 'pdf_path');

        $acceptedJobs = $hasStatusColumn
            ? (clone $quotesQuery)->where('status', 'accepted')->orWhere('status', 'work_complete')->count()
            : 0;

        $recentQuotes = (clone $quotesQuery)
            ->latest()
            ->limit(8)
            ->get();

        $organisationNames = Organisations::query()
            ->whereIn('id', $recentQuotes->pluck('organisation_id')->filter()->unique()->values())
            ->pluck('name', 'id');

        $recentQuoteRows = $recentQuotes->map(function ($quote) use ($hasStatusColumn, $hasJobNameColumn, $hasCustomerNameColumn, $hasPdfPathColumn, $organisationNames) {
            $status = $hasStatusColumn ? (string) ($quote->status ?? 'draft') : 'draft';
            $status = in_array($status, ['draft', 'sent', 'accepted', 'declined', 'work_complete'], true) ? $status : 'draft';

            $jobName = 'Quote #'.$quote->id;

            if ($hasJobNameColumn && trim((string) ($quote->job_name ?? '')) !== '') {
                $jobName = trim((string) $quote->job_name);
            } elseif ($quote->variant_key) {
                $jobName = Str::title(str_replace(['_', '-'], ' ', (string) $quote->variant_key));
            }

            $customerName = 'Customer not set';

            if ($hasCustomerNameColumn && trim((string) ($quote->customer_name ?? '')) !== '') {
                $customerName = trim((string) $quote->customer_name);
            } elseif ($quote->organisation_id) {
                $customerName = (string) ($organisationNames[$quote->organisation_id] ?? $customerName);
            }

            return [
                'id' => $quote->id,
                'job_name' => $jobName,
                'customer_name' => $customerName,
                'total_price' => (float) $quote->total_price / 100,
                'status' => $status,
                'has_pdf_snapshot' => $hasPdfPathColumn ? ! empty($quote->pdf_path) : false,
                'created_at' => $quote->created_at,
            ];
        });

        $templates = QuoteTemplates::query()
            ->where('created_by', (int) $user->id)
            ->when((int) ($user->organisation_id ?? 0) > 0, function ($query) use ($user) {
                $query->where('organisation_id', (int) $user->organisation_id);
            })
            ->latest()
            ->limit(6)
            ->get(['id', 'name', 'variant_key']);

        $isPro = DB::table('subscriptions')
            ->where('user_id', (int) $user->id)
            ->whereIn('stripe_status', ['active', 'trialing'])
            ->exists();

        return view('livewire.dashboard-overview', [
            'quotesThisWeek' => $quotesThisWeek,
            'totalValueQuoted' => $totalValueQuoted,
            'acceptedJobs' => $acceptedJobs,
            'recentQuoteRows' => $recentQuoteRows,
            'templates' => $templates,
            'isPro' => $isPro,
        ]);
    }
}

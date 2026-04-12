<?php

namespace App\Livewire;

use App\Models\Quotes;
use App\Models\User;
use App\Notifications\QuoteResponseNotification;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class QuoteResponse extends Component
{
    public string $uuid;
    public string $currentStatus;
    public string $rejectionReason = '';
    public bool $showDeclineModal = false;

    public function mount(string $uuid): void
    {
        $quote = Quotes::where('uuid', $uuid)->firstOrFail();
        $this->uuid = $uuid;
        $this->currentStatus = $quote->status ?? 'draft';
    }

    public function accept(): void
    {
        if (in_array($this->currentStatus, ['accepted', 'declined'])) {
            return;
        }

        $quote = Quotes::where('uuid', $this->uuid)->firstOrFail();

        DB::transaction(function () use ($quote) {
            $quote->update(['status' => 'accepted']);
        });

        $this->notifyTradeUser($quote, 'accepted');

        $this->currentStatus = 'accepted';
    }

    public function openDeclineModal(): void
    {
        $this->showDeclineModal = true;
    }

    public function closeDeclineModal(): void
    {
        $this->showDeclineModal = false;
        $this->rejectionReason = '';
    }

    public function cancelReject(): void
    {
        $this->closeDeclineModal();
    }

    public function reject(): void
    {
        if (in_array($this->currentStatus, ['accepted', 'declined'])) {
            return;
        }

        $this->validate([
            'rejectionReason' => ['nullable', 'string', 'max:500'],
        ]);

        $quote = Quotes::where('uuid', $this->uuid)->firstOrFail();

        DB::transaction(function () use ($quote) {
            $quote->update(['status' => 'declined']);
        });

        $this->notifyTradeUser($quote, 'declined', $this->rejectionReason ?: null);

        $this->currentStatus = 'declined';
        $this->showDeclineModal = false;
    }

    private function notifyTradeUser(Quotes $quote, string $decision, ?string $reason = null): void
    {
        $user = User::find($quote->created_by);

        if ($user) {
            $user->notify(new QuoteResponseNotification($quote, $decision, $reason));
        }
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.quote-response');
    }
}

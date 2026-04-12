<?php

namespace App\Livewire;

use Livewire\Component;

class NotificationList extends Component
{
    public bool $expanded = false;

    public function toggle(): void
    {
        $this->expanded = ! $this->expanded;
    }

    public function markAllRead(): void
    {
        auth()->user()->unreadNotifications->markAsRead();
    }

    public function markRead(string $id): void
    {
        auth()->user()->notifications()->where('id', $id)->first()?->markAsRead();
    }

    public function openQuote(int $quoteId): void
    {
        redirect()->route('quotes.edit', $quoteId);
    }

    public function render(): \Illuminate\View\View
    {
        $unreadCount = auth()->user()->unreadNotifications()->count();

        $notifications = $this->expanded
            ? auth()->user()->notifications()->latest()->take(10)->get()
            : collect();

        return view('livewire.notification-list', compact('unreadCount', 'notifications'));
    }
}

<?php

namespace App\Notifications;

use App\Models\Quotes;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QuoteResponseNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Quotes $quote,
        private readonly string $decision,
        private readonly ?string $reason = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $customerName = $this->quote->customer_name ?: 'Your customer';
        $jobName      = $this->quote->job_name ?: 'your job';

        $subject = "{$customerName} has {$this->decision} the quote for {$jobName}";

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting("Hi {$notifiable->name},")
            ->line("{$customerName} has **{$this->decision}** the quote for *{$jobName}*.");

        if ($this->reason) {
            $mail->line('**Reason:** ' . $this->reason);
        }

        return $mail
            ->action('View Quote', route('quotes.edit', ['quote' => $this->quote->id]))
            ->line('Log in to your TradeQuote dashboard to view the full details.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'quote_id'      => $this->quote->id,
            'customer_name' => $this->quote->customer_name,
            'job_name'      => $this->quote->job_name,
            'decision'      => $this->decision,
            'reason'        => $this->reason,
        ];
    }
}

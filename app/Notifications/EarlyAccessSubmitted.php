<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EarlyAccessSubmitted extends Notification
{
    use Queueable;

    public function __construct(private readonly $data) {}

    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Early Access Registration – ' . $this->data['name'])
            ->greeting('New Early Access Sign-Up!')
            ->line('A new user has registered for early access on QuoteFlow.')
            ->line('---')
            ->line('**Name:** ' . $this->data['name'])
            ->line('**Business:** ' . $this->data['business_name'])
            ->line('**Email:** ' . $this->data['email'])
            ->line('**Current quoting method:** ' . (trim($this->data['quoting_method']) !== '' ? $this->data['quoting_method'] : 'Not provided'))
            ->line('**Submitted at:** ' . now()->format('d M Y, H:i T'));
    }
}

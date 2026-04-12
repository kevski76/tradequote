<?php

namespace App\Notifications;

use App\Models\FeedbackSubmission;
use App\Models\Quotes;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FeedbackSubmitted extends Notification
{
    use Queueable;

    public function __construct(
        private readonly FeedbackSubmission $submission,
        private readonly Quotes $quote,
        private readonly string $googleReviewUrl,
    ) {}

    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('New Private Feedback - Quote #' . $this->quote->id)
            ->greeting('New private feedback received')
            ->line('A customer submitted private feedback from the review page.')
            ->line('---')
            ->line('**Quote ID:** ' . $this->quote->id)
            ->line('**Quote UUID:** ' . $this->quote->uuid)
            ->line('**Customer name:** ' . ($this->submission->customer_name !== '' ? $this->submission->customer_name : 'Not provided'))
            ->line('**Customer email:** ' . ($this->submission->customer_email !== '' ? $this->submission->customer_email : 'Not provided'))
            ->line('**Customer phone:** ' . ($this->submission->customer_phone !== '' ? $this->submission->customer_phone : 'Not provided'))
            ->line('**Message:**')
            ->line($this->submission->message)
            ->line('---')
            ->line('Submitted at: ' . $this->submission->created_at?->format('d M Y, H:i T'));

        if ($this->googleReviewUrl !== '') {
            $mail->action('Open Google Review Link', $this->googleReviewUrl);
        }

        return $mail;
    }
}

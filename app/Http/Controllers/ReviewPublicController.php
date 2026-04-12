<?php

namespace App\Http\Controllers;

use App\Models\FeedbackSubmission;
use App\Models\Organisations;
use App\Models\Quotes;
use App\Models\User;
use App\Notifications\FeedbackSubmitted;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class ReviewPublicController extends Controller
{
    public function show(string $uuid): View
    {
        $quote = $this->resolvePublicQuote($uuid);

        $organisation = (int) $quote->organisation_id > 0
            ? Organisations::query()->find((int) $quote->organisation_id)
            : null;

        [$googleReviewUrl] = $this->resolveReviewConfig($organisation);

        return view('reviews.public', [
            'quote' => $quote,
            'googleReviewUrl' => $googleReviewUrl,
            'organisation' => $organisation,
        ]);
    }

    public function feedbackForm(string $uuid): View
    {
        $quote = $this->resolvePublicQuote($uuid);

        return view('reviews.feedback', [
            'quote' => $quote,
        ]);
    }

    public function submitFeedback(Request $request, string $uuid): RedirectResponse
    {
        $quote = $this->resolvePublicQuote($uuid);

        // Honeypot field should stay empty; if filled, silently return success-like response.
        if (trim((string) $request->input('website', '')) !== '') {
            return redirect()
                ->route('review.feedback', ['uuid' => $uuid])
                ->with('feedback_submitted', true);
        }

        $rateLimitKey = 'review-feedback:'.$uuid.':'.$request->ip();

        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);

            return back()
                ->withErrors(['message' => 'Please wait '.$seconds.' seconds and try again.'])
                ->withInput();
        }

        RateLimiter::hit($rateLimitKey, 600);

        $organisation = (int) $quote->organisation_id > 0
            ? Organisations::query()->find((int) $quote->organisation_id)
            : null;

        [$googleReviewUrl, $recipient] = $this->resolveReviewConfig($organisation);

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'regex:/^[\+]?[\d\s\-\(\)\.]{7,20}$/'],
            'message' => ['required', 'string', 'min:3', 'max:2000'],
        ]);

        $submission = FeedbackSubmission::query()->create([
            'quote_id' => (int) $quote->id,
            'organisation_id' => (int) ($quote->organisation_id ?? 0),
            'quote_uuid' => (string) $quote->uuid,
            'customer_name' => trim((string) ($validated['name'] ?? '')),
            'customer_email' => trim((string) ($validated['email'] ?? '')),
            'customer_phone' => trim((string) ($validated['phone'] ?? '')),
            'message' => trim((string) $validated['message']),
        ]);

        if ($recipient !== '') {
            Notification::route('mail', $recipient)
                ->notify(new FeedbackSubmitted($submission, $quote, $googleReviewUrl));
        }

        return redirect()
            ->route('review.feedback', ['uuid' => $uuid])
            ->with('feedback_submitted', true);
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function resolveReviewConfig(?Organisations $organisation): array
    {
        $defaults = is_array($organisation?->quote_defaults) ? $organisation->quote_defaults : [];
        $globalDefaults = is_array($defaults['global'] ?? null) ? $defaults['global'] : [];

        $googleReviewUrl = trim((string) ($globalDefaults['google_review_url'] ?? ''));
        $feedbackRecipient = trim((string) ($globalDefaults['feedback_notification_email'] ?? ''));

        if ($feedbackRecipient === '' && $organisation && (int) $organisation->owner_id > 0) {
            $owner = User::query()->find((int) $organisation->owner_id);
            $feedbackRecipient = trim((string) ($owner?->email ?? ''));
        }

        if ($feedbackRecipient === '') {
            $feedbackRecipient = trim((string) config('mail.from.address', ''));
        }

        return [$googleReviewUrl, $feedbackRecipient];
    }

    private function resolvePublicQuote(string $token): Quotes
    {
        $quote = Quotes::query()
            ->where('uuid', $token)
            ->first();

        if ($quote) {
            return $quote;
        }

        if (ctype_digit($token)) {
            return Quotes::query()->findOrFail((int) $token);
        }

        abort(404);
    }
}

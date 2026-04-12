<?php

namespace App\Http\Controllers;

use App\Models\FeedbackSubmission;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class FeedbackSubmissionController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $organisationId = (int) ($user?->organisation_id ?? 0);

        $feedback = FeedbackSubmission::query()
            ->when($organisationId > 0, fn ($query) => $query->where('organisation_id', $organisationId))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('feedback.index', [
            'feedback' => $feedback,
        ]);
    }
}

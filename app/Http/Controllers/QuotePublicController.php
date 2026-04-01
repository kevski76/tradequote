<?php

namespace App\Http\Controllers;

use App\Models\Quotes;
use Illuminate\View\View;

class QuotePublicController extends Controller
{
    public function show(string $uuid): View
    {
        $quote = Quotes::query()
            ->where('uuid', $uuid)
            ->firstOrFail();

        $breakdown = is_array($quote->calculation_data) ? $quote->calculation_data : [];

        return view('quotes.public', compact('quote', 'breakdown'));
    }
}

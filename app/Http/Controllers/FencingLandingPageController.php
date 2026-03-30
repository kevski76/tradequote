<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class FencingLandingPageController extends Controller
{
    /**
     * Display the fencing landing page.
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        return view('fencing-landing-page');
    }
}

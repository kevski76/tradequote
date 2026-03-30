<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class WelcomeController extends Controller
{
    /**
     * Display the welcome page.
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        return view('welcome');
    }
}

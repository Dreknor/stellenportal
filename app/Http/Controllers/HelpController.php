<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class HelpController extends Controller
{
    /**
     * Display the help page.
     */
    public function index(): View
    {
        return view('help.index');
    }
}


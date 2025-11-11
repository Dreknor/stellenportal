<?php

namespace App\Http\Controllers;

use App\Models\CreditPackage;
use Illuminate\Http\Request;

class PublicPricingController extends Controller
{
    /**
     * Display pricing information for credit packages
     */
    public function index()
    {
        // Get active packages grouped by cooperative membership
        $standardPackages = CreditPackage::active()
            ->where('for_cooperative_members', false)
            ->orderBy('credits', 'asc')
            ->get();

        $cooperativePackages = CreditPackage::active()
            ->where('for_cooperative_members', true)
            ->orderBy('credits', 'asc')
            ->get();

        return view('public.pricing', [
            'standardPackages' => $standardPackages,
            'cooperativePackages' => $cooperativePackages,
        ]);
    }
}


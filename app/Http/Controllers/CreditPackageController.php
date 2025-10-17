<?php

namespace App\Http\Controllers;

use App\Models\CreditPackage;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CreditPackageController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of credit packages
     */
    public function index()
    {
        $this->authorize('viewAny', CreditPackage::class);

        $packages = CreditPackage::orderBy('credits', 'asc')->get();

        return view('credits.packages.index', compact('packages'));
    }

    /**
     * Show the form for creating a new credit package
     */
    public function create()
    {
        $this->authorize('create', CreditPackage::class);

        return view('credits.packages.create');
    }

    /**
     * Store a newly created credit package
     */
    public function store(Request $request)
    {
        $this->authorize('create', CreditPackage::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'credits' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0.01',
            'is_active' => 'boolean',
        ]);

        $package = CreditPackage::create($validated);

        return redirect()->route('credits.packages.index')
            ->with('success', 'Guthaben-Paket wurde erfolgreich erstellt.');
    }

    /**
     * Show the form for editing the specified credit package
     */
    public function edit(CreditPackage $package)
    {
        $this->authorize('update', $package);

        return view('credits.packages.edit', compact('package'));
    }

    /**
     * Update the specified credit package
     */
    public function update(Request $request, CreditPackage $package)
    {
        $this->authorize('update', $package);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'credits' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0.01',
            'is_active' => 'boolean',
        ]);

        $package->update($validated);

        return redirect()->route('credits.packages.index')
            ->with('success', 'Guthaben-Paket wurde erfolgreich aktualisiert.');
    }

    /**
     * Remove the specified credit package
     */
    public function destroy(CreditPackage $package)
    {
        $this->authorize('delete', $package);

        $package->delete();

        return redirect()->route('credits.packages.index')
            ->with('success', 'Guthaben-Paket wurde erfolgreich gelöscht.');
    }
}

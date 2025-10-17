<?php

namespace App\Http\Controllers\Organizations;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        // Wenn User direkt Organisationen zugeordnet ist
        if ($user->organizations->isNotEmpty()) {
            $organizations = $user->organizations;
            $isReadOnly = false;
        }
        // Wenn User nur Einrichtungen zugeordnet ist (aber keine Organisationen)
        elseif ($user->facilities->isNotEmpty()) {
            // Hole die Träger der zugeordneten Einrichtungen
            $organizations = Organization::whereIn('id', $user->facilities->pluck('organization_id'))->get();
            $isReadOnly = true;
        }
        else {
            $organizations = collect();
            $isReadOnly = false;
        }

        return view('organizations.index',
            compact('organizations', 'isReadOnly')
        );
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string|max:1000',
            'street' => 'required|string|max:255',
            'number' => 'required|string|max:20',
            'city' => 'required|string|max:100',
            'zip_code' => 'required|string|max:20',
        ]);


        $organization = Organization::create($request->only([
            'name', 'email', 'phone', 'website', 'description'
        ]));

        $organization->address()->create($request->only([
            'street', 'number', 'city', 'zip_code'
        ]));

        $user = auth()->user();
        $user->organizations()->attach($organization->id);

        return redirect()->route('organizations.index')
            ->with('success', 'Organization created successfully.');

    }

    /**
     * Display the specified resource.
     */
    public function show(Organization $organization)
    {
        if (!auth()->user()->organizations->contains($organization)) {
            Log::info('Unberechtigter Organisationszugriff:', [
                'user' => auth()->user(),
                'organization' => $organization,
            ]);
            return redirect()->route('organizations.index')
                ->with('error', 'Kein zugriff auf diese Organisation.');
        }

        $organization->load(['address', 'users', 'facilities']);
        $users = $organization->users;
        $facilities = $organization->facilities;

        return view('organizations.show', compact('organization', 'users', 'facilities'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Organization $organization)
    {
        if (!auth()->user()->organizations->contains($organization)) {
            Log::info('Unberechtigter Organisationszugriff:', [
                'user' => auth()->user(),
                'organization' => $organization,
            ]);
            return redirect()->route('organizations.index')
                ->with('error', 'Kein zugriff auf diese Organisation.');
        }

        return view('organizations.edit', compact('organization'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Organization $organization)
    {
        if (!auth()->user()->organizations->contains($organization)) {
            Log::info('Unberechtigter Organisationszugriff:', [
                'user' => auth()->user(),
                'organization' => $organization,
            ]);
            return redirect()->route('organizations.index')
                ->with('error', 'Kein zugriff auf diese Organisation.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string|max:1000',
            'street' => 'required|string|max:255',
            'number' => 'required|string|max:20',
            'city' => 'required|string|max:100',
            'zip_code' => 'required|string|max:20',
            'header_image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'remove_header_image' => 'nullable|boolean',
        ]);

        $organization->update($request->only([
            'name', 'email', 'phone', 'website', 'description'
        ]));

        if ($organization->address) {
            $organization->address->update($request->only([
                'street', 'number', 'city', 'zip_code'
            ]));
        } else {
            $organization->address()->create($request->only([
                'street', 'number', 'city', 'zip_code'
            ]));
        }

        // Handle header image removal
        if ($request->input('remove_header_image')) {
            $organization->clearMediaCollection('header_image');
        }

        // Handle header image upload
        if ($request->hasFile('header_image')) {
            $organization->addMediaFromRequest('header_image')
                ->toMediaCollection('header_image');
        }

        return redirect()->route('organizations.edit', $organization)
            ->with('success', 'Organisation erfolgreich aktualisiert.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organization $organization)
    {
        //
    }
}

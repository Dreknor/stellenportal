<?php

namespace App\Http\Controllers\Facilities;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FacilityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        // Wenn User spezifischen Einrichtungen zugeordnet ist, nur diese anzeigen
        if ($user->facilities->isNotEmpty()) {
            $facilities = $user->facilities;
        } else {
            // Sonst alle Einrichtungen der Organisationen anzeigen
            $facilities = Facility::whereIn('organization_id', $user->organizations->pluck('id'))->get();
        }

        return view('facilities.index', compact('facilities'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        // Nur genehmigte Organisationen
        $organizations = $user->organizations()->where('is_approved', true)->get();

        // Nur Benutzer mit direkter Organisationszuordnung dürfen Einrichtungen anlegen
        if ($organizations->isEmpty()) {
            // Prüfen ob User überhaupt Organisationen hat
            if ($user->organizations()->count() === 0) {
                return redirect()->route('organizations.index')
                    ->with('error', 'Sie müssen einer Organisation zugeordnet sein, um Einrichtungen anlegen zu können.');
            }

            // User hat Organisationen, aber keine ist genehmigt
            return redirect()->route('organizations.index')
                ->with('error', 'Ihre Organisation(en) müssen erst vom Administrator genehmigt werden, bevor Sie Einrichtungen anlegen können.');
        }

        return view('facilities.create', compact('organizations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        // Sicherstellen, dass User einer genehmigten Organisation zugeordnet ist
        $approvedOrganizations = $user->organizations()->where('is_approved', true)->get();
        if ($approvedOrganizations->isEmpty()) {
            return redirect()->route('facilities.index')
                ->with('error', 'Sie müssen einer genehmigten Organisation zugeordnet sein, um Einrichtungen anlegen zu können.');
        }

        $request->validate([
            'organization_id' => 'required|exists:organizations,id',
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

        // Prüfen ob User der Organisation zugeordnet ist und diese genehmigt ist
        $organization = Organization::findOrFail($request->organization_id);
        if (!$user->organizations->contains($organization)) {
            return redirect()->route('facilities.index')
                ->with('error', 'Sie haben keine Berechtigung für diese Organisation.');
        }

        if (!$organization->canUseFeatures()) {
            return redirect()->route('facilities.index')
                ->with('error', 'Diese Organisation muss erst vom Administrator genehmigt werden.');
        }

        $facility = Facility::create($request->only([
            'organization_id', 'name', 'email', 'phone', 'website', 'description'
        ]));

        $facility->address()->create($request->only([
            'street', 'number', 'city', 'zip_code'
        ]));

        return redirect()->route('facilities.show', $facility)
            ->with('success', 'Einrichtung erfolgreich erstellt.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Facility $facility)
    {
        $user = auth()->user();

        // Prüfen ob User Zugriff hat (über Organisation oder direkt über Einrichtung)
        if (!$this->userHasAccessToFacility($user, $facility)) {
            Log::info('Unberechtigter Einrichtungszugriff:', [
                'user' => $user,
                'facility' => $facility,
            ]);
            return redirect()->route('facilities.index')
                ->with('error', 'Kein Zugriff auf diese Einrichtung.');
        }

        $facility->load(['address', 'users', 'organization']);
        $users = $facility->users;

        return view('facilities.show', compact('facility', 'users'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Facility $facility)
    {
        $user = auth()->user();

        if (!$this->userHasAccessToFacility($user, $facility)) {
            Log::info('Unberechtigter Einrichtungszugriff:', [
                'user' => $user,
                'facility' => $facility,
            ]);
            return redirect()->route('facilities.index')
                ->with('error', 'Kein Zugriff auf diese Einrichtung.');
        }

        $organizations = $user->organizations;
        return view('facilities.edit', compact('facility', 'organizations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Facility $facility)
    {
        $user = auth()->user();

        if (!$this->userHasAccessToFacility($user, $facility)) {
            Log::info('Unberechtigter Einrichtungszugriff:', [
                'user' => $user,
                'facility' => $facility,
            ]);
            return redirect()->route('facilities.index')
                ->with('error', 'Kein Zugriff auf diese Einrichtung.');
        }


        $request->validate([
            'organization_id' => 'required|exists:organizations,id',
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

        $facility->update($request->only([
            'organization_id', 'name', 'email', 'phone', 'website', 'description'
        ]));

        if ($facility->address) {
            $facility->address->update($request->only([
                'street', 'number', 'city', 'zip_code'
            ]));
        } else {
            $facility->address()->create($request->only([
                'street', 'number', 'city', 'zip_code'
            ]));
        }

        // Handle header image removal
        if ($request->input('remove_header_image')) {
            $facility->clearMediaCollection('header_image');
        }

        // Handle header image upload
        if ($request->hasFile('header_image')) {
            $facility->addMediaFromRequest('header_image')
                ->toMediaCollection('header_image');
        }

        return redirect()->route('facilities.edit', $facility)
            ->with('success', 'Einrichtung erfolgreich aktualisiert.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Facility $facility)
    {
        $user = auth()->user();

        if (!$user->organizations->contains($facility->organization)) {
            return redirect()->route('facilities.index')
                ->with('error', 'Keine Berechtigung zum Löschen.');
        }

        $facility->delete();

        return redirect()->route('facilities.index')
            ->with('success', 'Einrichtung erfolgreich gelöscht.');
    }

    /**
     * Check if user has access to facility
     */
    private function userHasAccessToFacility($user, Facility $facility): bool
    {
        // User hat Zugriff wenn er der Einrichtung direkt zugeordnet ist
        if ($user->facilities->contains($facility)) {
            return true;
        }

        // Oder wenn er der Organisation zugeordnet ist
        if ($user->organizations->contains($facility->organization)) {
            return true;
        }

        return false;
    }
}

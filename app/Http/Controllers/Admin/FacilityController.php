<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Http\Controllers\Admin\Concerns\ManagesAddress;
use App\Models\Organization;
use App\Services\GeocodingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class FacilityController extends Controller
{
    use ManagesAddress;

    public function geocode(Facility $facility, GeocodingService $geocoding)
    {
        return $this->runGeocoding($facility, $geocoding);
    }

    /**
     * Übernimmt die Koordinaten der Organisation für die Adresse der Einrichtung.
     */
    public function copyOrganizationCoordinates(Facility $facility, GeocodingService $geocoding): RedirectResponse
    {
        $address = $facility->address()->first();

        if (! $address) {
            return back()->with('error', 'Bitte legen Sie zuerst eine Adresse für die Einrichtung an.');
        }

        $organizationAddress = $facility->organization()->first()?->address()->first();

        if (! $organizationAddress || ! $organizationAddress->latitude || ! $organizationAddress->longitude) {
            return back()->with('error', 'Für die Organisation sind keine Koordinaten hinterlegt.');
        }

        $address->latitude = $organizationAddress->latitude;
        $address->longitude = $organizationAddress->longitude;
        // Übernommene Koordinaten ersetzen einen früheren Geocoding-Fehler
        $address->geocoding_error = null;
        $address->geocoding_failed_at = null;
        // saveQuietly, damit der AddressObserver die übernommenen Koordinaten nicht neu geocodiert
        $address->saveQuietly();
        $geocoding->generateMapImage($address);

        return back()->with('success', 'Koordinaten der Organisation erfolgreich übernommen.');
    }

    public function index(Request $request)
    {
        $query = Facility::with(['organization', 'address', 'creditBalance']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by organization
        if ($request->filled('organization')) {
            $query->where('organization_id', $request->organization);
        }

        $facilities = $query->latest()->paginate(20)->withQueryString();
        $organizations = Organization::all();

        return view('admin.facilities.index', compact('facilities', 'organizations'));
    }

    public function show(Facility $facility)
    {
        $facility->load([
            'organization.address',
            'users',
            'address',
            'jobPostings',
            'creditBalance',
            'creditTransactions',
            'audits'
        ]);

        return view('admin.facilities.show', compact('facility'));
    }

    public function edit(Facility $facility)
    {
        $organizations = Organization::all();
        return view('admin.facilities.edit', compact('facility', 'organizations'));
    }

    public function update(Request $request, Facility $facility)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'organization_id' => ['required', 'exists:organizations,id'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'description' => ['nullable', 'string'],
            'header_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'remove_header_image' => ['nullable', 'boolean'],
            'remove_logo' => ['nullable', 'boolean'],
            ...$this->addressRules(),
        ]);

        $facility->update(Arr::except($validated, ['street', 'number', 'zip_code', 'city', 'state']));
        $this->saveAddress($facility, $validated);

        // Handle header image removal
        if ($request->input('remove_header_image')) {
            $facility->clearMediaCollection('header_image');
        }

        // Handle logo removal
        if ($request->input('remove_logo')) {
            $facility->clearMediaCollection('logo');
        }

        // Handle header image upload
        if ($request->hasFile('header_image')) {
            $facility->addMediaFromRequest('header_image')
                ->toMediaCollection('header_image');
        }

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $facility->addMediaFromRequest('logo')
                ->toMediaCollection('logo');
        }

        return redirect()->route('admin.facilities.show', $facility)
            ->with('success', 'Einrichtung erfolgreich aktualisiert.');
    }

    public function destroy(Facility $facility)
    {
        // Check if facility has active job postings
        if ($facility->jobPostings()->whereIn('status', ['active', 'draft'])->count() > 0) {
            return back()->with('error', 'Einrichtung kann nicht gelöscht werden, da sie noch aktive Stellenausschreibungen hat.');
        }

        $facility->delete();

        return redirect()->route('admin.facilities.index')
            ->with('success', 'Einrichtung erfolgreich gelöscht.');
    }
}

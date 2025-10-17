<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\Organization;
use Illuminate\Http\Request;

class FacilityController extends Controller
{
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
            'organization',
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
        ]);

        $facility->update($validated);

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

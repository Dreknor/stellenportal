<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function index(Request $request)
    {
        $query = Organization::with(['users', 'facilities', 'creditBalance']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $organizations = $query->latest()->paginate(20)->withQueryString();

        return view('admin.organizations.index', compact('organizations'));
    }

    public function show(Organization $organization)
    {
        $organization->load([
            'users',
            'facilities.address',
            'creditBalance',
            'creditTransactions',
            'address',
            'audits',
            'approvedBy'
        ]);

        return view('admin.organizations.show', compact('organization'));
    }

    public function edit(Organization $organization)
    {
        $users = User::all();
        return view('admin.organizations.edit', compact('organization', 'users'));
    }

    public function update(Request $request, Organization $organization)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_cooperative_member' => ['boolean'],
        ]);

        $organization->update($validated);

        return redirect()->route('admin.organizations.show', $organization)
            ->with('success', 'Organisation erfolgreich aktualisiert.');
    }

    public function destroy(Organization $organization)
    {
        // Check if organization has facilities
        if ($organization->facilities()->count() > 0) {
            return back()->with('error', 'Organisation kann nicht gelöscht werden, da sie noch Einrichtungen hat.');
        }

        $organization->delete();

        return redirect()->route('admin.organizations.index')
            ->with('success', 'Organisation erfolgreich gelöscht.');
    }

    public function approve(Organization $organization)
    {
        if ($organization->is_approved) {
            return back()->with('error', 'Organisation ist bereits bestätigt.');
        }

        $organization->update([
            'is_approved' => true,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Organisation erfolgreich bestätigt.');
    }

    public function unapprove(Organization $organization)
    {
        if (!$organization->is_approved) {
            return back()->with('error', 'Organisation ist bereits nicht bestätigt.');
        }

        $organization->update([
            'is_approved' => false,
            'approved_by' => null,
            'approved_at' => null,
        ]);

        return back()->with('success', 'Bestätigung erfolgreich zurückgezogen.');
    }
}

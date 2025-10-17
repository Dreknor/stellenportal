<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the permissions.
     */
    public function index()
    {
        $this->authorize('viewAny', Permission::class);

        $permissions = Permission::orderBy('name')->get()->groupBy(function($permission) {
            return explode(' ', $permission->name)[1] ?? 'other';
        });

        return view('permissions.index', compact('permissions'));
    }

    /**
     * Show the form for creating a new permission.
     */
    public function create()
    {
        $this->authorize('create', Permission::class);

        return view('permissions.create');
    }

    /**
     * Store a newly created permission.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Permission::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
        ]);

        Permission::create(['name' => $validated['name']]);

        return redirect()->route('permissions.index')
            ->with('success', 'Berechtigung wurde erfolgreich erstellt.');
    }

    /**
     * Display the specified permission.
     */
    public function show(Permission $permission)
    {
        $this->authorize('view', $permission);

        $permission->load('roles');

        return view('permissions.show', compact('permission'));
    }

    /**
     * Show the form for editing the specified permission.
     */
    public function edit(Permission $permission)
    {
        $this->authorize('update', $permission);

        return view('permissions.edit', compact('permission'));
    }

    /**
     * Update the specified permission.
     */
    public function update(Request $request, Permission $permission)
    {
        $this->authorize('update', $permission);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
        ]);

        $permission->update(['name' => $validated['name']]);

        return redirect()->route('permissions.index')
            ->with('success', 'Berechtigung wurde erfolgreich aktualisiert.');
    }

    /**
     * Remove the specified permission.
     */
    public function destroy(Permission $permission)
    {
        $this->authorize('delete', $permission);

        if ($permission->roles()->count() > 0) {
            return back()->with('error', 'Diese Berechtigung kann nicht gelöscht werden, da sie noch Rollen zugewiesen ist.');
        }

        $permission->delete();

        return redirect()->route('permissions.index')
            ->with('success', 'Berechtigung wurde erfolgreich gelöscht.');
    }
}

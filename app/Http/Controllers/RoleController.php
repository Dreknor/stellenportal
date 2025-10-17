<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the roles.
     */
    public function index()
    {
        $this->authorize('viewAny', Role::class);

        $roles = Role::withCount('permissions', 'users')->orderBy('name')->get();

        return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        $this->authorize('create', Role::class);

        $permissions = Permission::orderBy('name')->get()->groupBy(function($permission) {
            return explode(' ', $permission->name)[1] ?? 'other';
        });

        return view('roles.create', compact('permissions'));
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Role::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create(['name' => $validated['name']]);

        if (isset($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        return redirect()->route('roles.index')
            ->with('success', 'Rolle wurde erfolgreich erstellt.');
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role)
    {
        $this->authorize('view', $role);

        $role->load('permissions', 'users');

        return view('roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role)
    {
        $this->authorize('update', $role);

        $permissions = Permission::orderBy('name')->get()->groupBy(function($permission) {
            return explode(' ', $permission->name)[1] ?? 'other';
        });

        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update the specified role.
     */
    public function update(Request $request, Role $role)
    {
        $this->authorize('update', $role);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update(['name' => $validated['name']]);

        $role->syncPermissions($validated['permissions'] ?? []);

        return redirect()->route('roles.index')
            ->with('success', 'Rolle wurde erfolgreich aktualisiert.');
    }

    /**
     * Remove the specified role.
     */
    public function destroy(Role $role)
    {
        $this->authorize('delete', $role);

        if ($role->users()->count() > 0) {
            return back()->with('error', 'Diese Rolle kann nicht gelöscht werden, da sie noch Benutzern zugewiesen ist.');
        }

        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Rolle wurde erfolgreich gelöscht.');
    }
}


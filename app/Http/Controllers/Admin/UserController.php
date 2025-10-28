<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['roles', 'organizations', 'facilities']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->role($request->role);
        }

        // Filter by email verification
        if ($request->filled('verified')) {
            if ($request->verified === 'yes') {
                $query->whereNotNull('email_verified_at');
            } else {
                $query->whereNull('email_verified_at');
            }
        }

        $users = $query->latest()->paginate(20)->withQueryString();
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        $organizations = \App\Models\Organization::orderBy('name')->get();
        $facilities = \App\Models\Facility::with('organization')->orderBy('name')->get();

        return view('admin.users.create', compact('roles', 'organizations', 'facilities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'roles' => ['array'],
            'roles.*' => ['exists:roles,name'],
            'organizations' => ['array'],
            'organizations.*' => ['exists:organizations,id'],
            'facilities' => ['array'],
            'facilities.*' => ['exists:facilities,id'],
            'send_welcome_email' => ['boolean'],
        ]);

        // Store the plain password before hashing
        $plainPassword = $validated['password'];

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'change_password' => false,
        ]);

        if (isset($validated['roles'])) {
            $user->assignRole($validated['roles']);
        }

        // Attach organizations
        if (isset($validated['organizations'])) {
            $user->organizations()->attach($validated['organizations']);
        }

        // Attach facilities
        if (isset($validated['facilities'])) {
            $user->facilities()->attach($validated['facilities']);
        }

        // Send welcome email if requested
        if ($request->boolean('send_welcome_email')) {
            $user->load(['organizations', 'facilities.organization']);
            \Illuminate\Support\Facades\Mail::to($user->email)->queue(new \App\Mail\AdminUserCreatedMail($user, $plainPassword));
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Benutzer erfolgreich erstellt.');
    }

    public function show(User $user)
    {
        $user->load(['roles', 'permissions', 'organizations', 'facilities', 'audits']);

        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $organizations = \App\Models\Organization::orderBy('name')->get();
        $facilities = \App\Models\Facility::with('organization')->orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'roles', 'organizations', 'facilities'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'roles' => ['array'],
            'roles.*' => ['exists:roles,name'],
            'organizations' => ['array'],
            'organizations.*' => ['exists:organizations,id'],
            'facilities' => ['array'],
            'facilities.*' => ['exists:facilities,id'],
            'change_password' => ['boolean'],
        ]);

        $user->first_name = $validated['first_name'];
        $user->last_name = $validated['last_name'];
        $user->email = $validated['email'];
        $user->change_password = $validated['change_password'] ?? false;

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        if (isset($validated['roles'])) {
            $user->syncRoles($validated['roles']);
        }

        // Sync organizations
        if (isset($validated['organizations'])) {
            $user->organizations()->sync($validated['organizations']);
        } else {
            $user->organizations()->sync([]);
        }

        // Sync facilities
        if (isset($validated['facilities'])) {
            $user->facilities()->sync($validated['facilities']);
        } else {
            $user->facilities()->sync([]);
        }

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'Benutzer erfolgreich aktualisiert.');
    }

    public function destroy(User $user)
    {
        // Prevent deleting own account
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Sie können Ihr eigenes Konto nicht löschen.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Benutzer erfolgreich gelöscht.');
    }
}

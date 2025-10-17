<?php

namespace App\Http\Controllers\Organizations;

use App\Http\Controllers\Controller;
use App\Mail\UserAssignedToOrganizationMail;
use App\Mail\UserCreatedMail;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class OrganizationUserController extends Controller
{
    /**
     * Display a listing of users for the organization.
     */
    public function index(Organization $organization)
    {
        $users = $organization->users()->paginate(15);
        return view('organizations.users.index', compact('organization', 'users'));
    }

    /**
     * Store a newly created user for the organization.
     */
    public function store(Request $request, Organization $organization)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        // Check if user already exists
        $user = User::where('email', $request->email)->first();

        if ($user) {
            // User already exists, just attach to organization if not already attached
            if (!$user->organizations()->where('organization_id', $organization->id)->exists()) {
                $user->organizations()->attach($organization->id);

                // Send email to inform user about organization assignment
                Mail::to($user->email)->send(new UserAssignedToOrganizationMail($user, $organization));

                return redirect()->route('organizations.users.index', $organization)
                    ->with('success', 'Bestehender Benutzer wurde der Organisation zugewiesen und per E-Mail benachrichtigt.');
            } else {
                return redirect()->route('organizations.users.index', $organization)
                    ->with('primary', 'Benutzer ist bereits Mitglied dieser Organisation.');
            }
        }

        // Generate a random password
        $password = Str::random(12);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($password),
            'change_password' => true, // User must change password on first login
        ]);

        // Attach user to organization
        $user->organizations()->attach($organization->id);


        // Send email with login credentials
        Mail::to($user->email)->queue(new UserCreatedMail($user, $organization, $password));

        return redirect()->route('organizations.users.index', $organization)
            ->with('success', 'Benutzer erfolgreich erstellt und Zugangsdaten per E-Mail versendet.');
    }

    /**
     * Remove the user from the organization.
     */
    public function destroy(Organization $organization, User $user)
    {
        // Detach user from organization
        $user->organizations()->detach($organization->id);

        return redirect()->route('organizations.users.index', $organization)
            ->with('success', 'Benutzer wurde von der Organisation entfernt.');
    }
}

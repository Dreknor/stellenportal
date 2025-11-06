<?php

namespace App\Http\Controllers\Facilities;

use App\Http\Controllers\Controller;
use App\Mail\UserAssignedToFacilityMail;
use App\Mail\UserCreatedForFacilityMail;
use App\Models\Facility;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class FacilityUserController extends Controller
{
    /**
     * Display a listing of users for the facility.
     */
    public function index(Facility $facility)
    {
        // Prüfen ob User Zugriff hat
        $user = auth()->user();
        if (!$this->userHasAccessToFacility($user, $facility)) {
            return redirect()->route('facilities.index')
                ->with('error', 'Kein Zugriff auf diese Einrichtung.');
        }

        if (!$facility->organization->canUseFeatures()) {
            return redirect()->route('facilities.index')
                ->with('error', 'Die Organisation dieser Einrichtung muss erst vom Administrator genehmigt werden.');
        }

        $users = $facility->users()->paginate(15);
        return view('facilities.users.index', compact('facility', 'users'));
    }

    /**
     * Store a newly created user for the facility.
     */
    public function store(Request $request, Facility $facility)
    {
        // Prüfen ob User Zugriff hat
        $authUser = auth()->user();
        if (!$this->userHasAccessToFacility($authUser, $facility)) {
            return redirect()->route('facilities.index')
                ->with('error', 'Kein Zugriff auf diese Einrichtung.');
        }

        if (!$facility->organization->canUseFeatures()) {
            return redirect()->route('facilities.index')
                ->with('error', 'Die Organisation dieser Einrichtung muss erst vom Administrator genehmigt werden.');
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        // Check if user already exists
        $user = User::where('email', $request->email)->first();

        if ($user) {
            // User already exists, just attach to facility if not already attached
            if (!$user->facilities()->where('facility_id', $facility->id)->exists()) {
                $user->facilities()->attach($facility->id);

                // Send email to inform user about facility assignment
                Mail::to($user->email)->queue(new UserAssignedToFacilityMail($user, $facility));

                return redirect()->route('facilities.users.index', $facility)
                    ->with('success', 'Bestehender Benutzer wurde der Einrichtung zugewiesen und per E-Mail benachrichtigt.');
            } else {
                return redirect()->route('facilities.users.index', $facility)
                    ->with('primary', 'Benutzer ist bereits Mitglied dieser Einrichtung.');
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

        // Attach user to facility
        $user->facilities()->attach($facility->id);

        // Send email verification notification
        $user->sendEmailVerificationNotification();

        // Send email with login credentials
        Mail::to($user->email)->queue(new UserCreatedForFacilityMail($user, $facility, $password));

        return redirect()->route('facilities.users.index', $facility)
            ->with('success', 'Benutzer erfolgreich erstellt und Zugangsdaten per E-Mail versendet.');
    }

    /**
     * Remove the user from the facility.
     */
    public function destroy(Facility $facility, User $user)
    {
        // Prüfen ob User Zugriff hat
        $authUser = auth()->user();
        if (!$this->userHasAccessToFacility($authUser, $facility)) {
            return redirect()->route('facilities.index')
                ->with('error', 'Kein Zugriff auf diese Einrichtung.');
        }

        if (!$facility->organization->canUseFeatures()) {
            return redirect()->route('facilities.index')
                ->with('error', 'Die Organisation dieser Einrichtung muss erst vom Administrator genehmigt werden.');
        }

        // Detach user from facility
        $user->facilities()->detach($facility->id);

        return redirect()->route('facilities.users.index', $facility)
            ->with('success', 'Benutzer wurde von der Einrichtung entfernt.');
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

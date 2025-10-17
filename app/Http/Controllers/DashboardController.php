<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Organization;
use App\Models\Facility;
use App\Models\JobPosting;
use App\Models\CreditBalance;
use App\Models\CreditTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Basis-Statistiken für alle Benutzer
        $stats = [
            'active_job_postings' => JobPosting::where('status', JobPosting::STATUS_ACTIVE)->count(),
            'total_organizations' => Organization::count(),
            'total_facilities' => Facility::count(),
            'total_users' => User::count(),
        ];

        // Benutzerspezifische Daten
        $userStats = [];
        $recentJobPostings = collect();
        $recentTransactions = collect();
        $creditBalance = null;

        // Wenn Benutzer Organisationen hat
        $userOrganizations = $user->organizations;

        if ($userOrganizations->isNotEmpty()) {
            $organizationIds = $userOrganizations->pluck('id');
            $facilityIds = Facility::whereIn('organization_id', $organizationIds)->pluck('id');

            $userStats = [
                'my_facilities' => $facilityIds->count(),
                'my_job_postings' => JobPosting::whereIn('facility_id', $facilityIds)->count(),
                'my_active_postings' => JobPosting::whereIn('facility_id', $facilityIds)
                    ->where('status', JobPosting::STATUS_ACTIVE)
                    ->count(),
                'my_draft_postings' => JobPosting::whereIn('facility_id', $facilityIds)
                    ->where('status', JobPosting::STATUS_DRAFT)
                    ->count(),
            ];

            // Aktuelle Stellenanzeigen der Organisation(en)
            $recentJobPostings = JobPosting::whereIn('facility_id', $facilityIds)
                ->with('facility')
                ->latest('published_at')
                ->take(5)
                ->get();

            // Credit-Saldo der ersten/Haupt-Organisation
            $mainOrganization = $userOrganizations->first();
            if ($mainOrganization) {
                $creditBalance = CreditBalance::where('creditable_type', Organization::class)
                    ->where('creditable_id', $mainOrganization->id)
                    ->first();

                // Letzte Credit-Transaktionen
                $recentTransactions = CreditTransaction::where('creditable_type', Organization::class)
                    ->where('creditable_id', $mainOrganization->id)
                    ->with('user')
                    ->latest()
                    ->take(10)
                    ->get();
            }
        }

        // Wenn Benutzer Einrichtungen hat (aber keine Organisation)
        $userFacilities = $user->facilities;

        if ($userFacilities->isNotEmpty() && $userOrganizations->isEmpty()) {
            $facilityIds = $userFacilities->pluck('id');

            $userStats = [
                'my_facilities' => $facilityIds->count(),
                'my_job_postings' => JobPosting::whereIn('facility_id', $facilityIds)->count(),
                'my_active_postings' => JobPosting::whereIn('facility_id', $facilityIds)
                    ->where('status', JobPosting::STATUS_ACTIVE)
                    ->count(),
                'my_draft_postings' => JobPosting::whereIn('facility_id', $facilityIds)
                    ->where('status', JobPosting::STATUS_DRAFT)
                    ->count(),
            ];

            // Aktuelle Stellenanzeigen der Einrichtung(en)
            $recentJobPostings = JobPosting::whereIn('facility_id', $facilityIds)
                ->with('facility')
                ->latest('published_at')
                ->take(5)
                ->get();
        }

        // Neueste aktive Stellenanzeigen für alle Benutzer
        $latestJobPostings = JobPosting::where('status', JobPosting::STATUS_ACTIVE)
            ->with('facility.organization')
            ->latest('published_at')
            ->take(10)
            ->get();

        return view('dashboard', compact(
            'stats',
            'userStats',
            'recentJobPostings',
            'recentTransactions',
            'creditBalance',
            'latestJobPostings'
        ));
    }
}

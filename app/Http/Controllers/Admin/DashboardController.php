<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Organization;
use App\Models\Facility;
use App\Models\JobPosting;
use App\Models\CreditTransaction;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users_total' => User::count(),
            'users_verified' => User::whereNotNull('email_verified_at')->count(),
            'organizations_total' => Organization::count(),
            'facilities_total' => Facility::count(),
            'job_postings_active' => JobPosting::where('status', JobPosting::STATUS_ACTIVE)->count(),
            'job_postings_draft' => JobPosting::where('status', JobPosting::STATUS_DRAFT)->count(),
            'job_postings_expired' => JobPosting::where('status', JobPosting::STATUS_EXPIRED)->count(),
            'credits_total' => DB::table('credit_balances')->sum('balance'),
            'credits_used_today' => CreditTransaction::where('type', 'usage')
                ->whereDate('created_at', today())
                ->sum('amount'),
        ];

        // Recent users
        $recentUsers = User::with('roles')->latest()->take(5)->get();

        // Recent job postings
        $recentJobPostings = JobPosting::with('facility.organization')
            ->latest('published_at')
            ->take(5)
            ->get();

        // Recent credit transactions
        $recentTransactions = CreditTransaction::with(['transactionable', 'user'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentJobPostings', 'recentTransactions'));
    }
}

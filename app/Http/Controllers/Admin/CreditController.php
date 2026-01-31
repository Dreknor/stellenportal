<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CreditTransaction;
use App\Models\CreditBalance;
use App\Models\Facility;
use App\Models\Organization;
use App\Services\CreditService;
use Illuminate\Http\Request;

class CreditController extends Controller
{
    public function __construct(
        protected CreditService $creditService
    ) {
    }

    public function index(Request $request)
    {
        // Get all credit balances with existing creditable entities
        $organizationBalances = CreditBalance::where('creditable_type', 'App\Models\Organization')
            ->with('creditable')
            ->whereHas('creditable')
            ->get();

        $facilityBalances = CreditBalance::where('creditable_type', 'App\Models\Facility')
            ->with('creditable.organization')
            ->whereHas('creditable')
            ->get();

        return view('admin.credits.index', compact('organizationBalances', 'facilityBalances'));
    }

    public function transactions(Request $request)
    {
        $query = CreditTransaction::with(['transactionable', 'user']);

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by transactionable type
        if ($request->filled('transactionable_type')) {
            if ($request->transactionable_type === 'Organization') {
                $query->where('transactionable_type', 'App\Models\Organization');
            } elseif ($request->transactionable_type === 'Facility') {
                $query->where('transactionable_type', 'App\Models\Facility');
            }
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->latest()->paginate(50)->withQueryString();

        return view('admin.credits.transactions', compact('transactions'));
    }

    public function grant()
    {
        $organizations = Organization::with('creditBalance')->get();
        $facilities = Facility::with('creditBalance', 'organization')->get();

        return view('admin.credits.grant', compact('organizations', 'facilities'));
    }

    public function storeGrant(Request $request)
    {
        $validated = $request->validate([
            'target_type' => ['required', 'in:organization,facility'],
            'target_id' => ['required', 'integer'],
            'amount' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            if ($validated['target_type'] === 'organization') {
                $organization = Organization::findOrFail($validated['target_id']);
                $this->creditService->addCredits(
                    $organization,
                    $validated['amount'],
                    auth()->user(),
                    $validated['description'] ?? 'Admin-Gutschrift'
                );
                $message = "Guthaben erfolgreich an Organisation {$organization->name} gutgeschrieben.";
            } else {
                $facility = Facility::findOrFail($validated['target_id']);
                $this->creditService->addCredits(
                    $facility,
                    $validated['amount'],
                    auth()->user(),
                    $validated['description'] ?? 'Admin-Gutschrift'
                );
                $message = "Guthaben erfolgreich an Einrichtung {$facility->name} gutgeschrieben.";
            }

            return redirect()->route('admin.credits.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function revoke()
    {
        $organizations = Organization::with('creditBalance')->get();
        $facilities = Facility::with('creditBalance', 'organization')->get();

        return view('admin.credits.revoke', compact('organizations', 'facilities'));
    }

    public function storeRevoke(Request $request)
    {
        $validated = $request->validate([
            'target_type' => ['required', 'in:organization,facility'],
            'target_id' => ['required', 'integer'],
            'amount' => ['required', 'integer', 'min:1'],
            'reason' => ['required', 'string', 'max:500'],
        ]);

        try {
            if ($validated['target_type'] === 'organization') {
                $organization = Organization::findOrFail($validated['target_id']);
                $currentBalance = $organization->getCurrentCreditBalance();

                // Prüfen ob genug Guthaben vorhanden ist
                if ($currentBalance < $validated['amount']) {
                    return back()->with('error', "Die Organisation hat nur {$currentBalance} Guthaben. Es können nicht {$validated['amount']} Guthaben entzogen werden.")
                        ->withInput();
                }

                $this->creditService->adjustCredits(
                    $organization,
                    -$validated['amount'], // Negativ für Abzug
                    auth()->user(),
                    'Guthaben entzogen: ' . $validated['reason']
                );
                $message = "{$validated['amount']} Guthaben erfolgreich von Organisation {$organization->name} entzogen.";
            } else {
                $facility = Facility::findOrFail($validated['target_id']);
                $currentBalance = $facility->getCurrentCreditBalance();

                // Prüfen ob genug Guthaben vorhanden ist
                if ($currentBalance < $validated['amount']) {
                    return back()->with('error', "Die Einrichtung hat nur {$currentBalance} Guthaben. Es können nicht {$validated['amount']} Guthaben entzogen werden.")
                        ->withInput();
                }

                $this->creditService->adjustCredits(
                    $facility,
                    -$validated['amount'], // Negativ für Abzug
                    auth()->user(),
                    'Guthaben entzogen: ' . $validated['reason']
                );
                $message = "{$validated['amount']} Guthaben erfolgreich von Einrichtung {$facility->name} entzogen.";
            }

            return redirect()->route('admin.credits.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }
}

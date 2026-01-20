<?php

namespace App\Http\Controllers;

use App\Models\CreditPackage;
use App\Models\Facility;
use App\Models\Organization;
use App\Services\CreditService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CreditController extends Controller
{
    use AuthorizesRequests;

    protected $creditService;

    public function __construct(CreditService $creditService)
    {
        $this->creditService = $creditService;
    }

    /**
     * Show credit purchase form for facility
     */
    public function showFacilityPurchase(Facility $facility)
    {
        if (!$facility->organization->canUseFeatures()) {
            return redirect()->route('organizations.show', $facility->organization)
                ->with('error', 'Die Organisation dieser Einrichtung muss erst vom Administrator genehmigt werden, bevor Sie Guthaben aufladen können.');
        }

        $this->authorize('purchaseCredits', $facility);

        $packages = CreditPackage::active()
            ->availableFor($facility->organization)
            ->orderBy('credits', 'asc')
            ->get();

        // Add purchase availability information to packages
        $packages = $packages->map(function ($package) use ($facility) {
            $package->can_be_purchased = $package->canBePurchasedBy($facility);
            $package->remaining_purchases = $package->getRemainingPurchasesFor($facility);
            return $package;
        });

        $balance = $facility->getCurrentCreditBalance();

        return view('credits.purchase.facility', compact('facility', 'packages', 'balance'));
    }

    /**
     * Purchase credits for facility
     */
    public function purchaseFacilityCredits(Request $request, Facility $facility)
    {
        if (!$facility->organization->canUseFeatures()) {
            return redirect()->route('organizations.show', $facility->organization)
                ->with('error', 'Die Organisation dieser Einrichtung muss erst vom Administrator genehmigt werden, bevor Sie Guthaben aufladen können.');
        }

        $this->authorize('purchaseCredits', $facility);

        $validated = $request->validate([
            'credit_package_id' => 'required|exists:credit_packages,id',
            'note' => 'nullable|string|max:500',
        ]);

        $package = CreditPackage::findOrFail($validated['credit_package_id']);

        if (!$package->is_active) {
            return back()->with('error', 'Dieses Paket ist nicht mehr verfügbar.');
        }

        try {
            /** @var \App\Models\User $user */
            $user = auth()->user();
            $transaction = $this->creditService->purchaseCredits(
                $facility,
                $package,
                $user,
                $validated['note'] ?? null
            );

            return redirect()->route('credits.facility.transactions', $facility)
                ->with('success', 'Guthaben wurden erfolgreich aufgeladen!');
        } catch (\Exception $e) {
            return back()->with('error', 'Fehler beim Aufladen: ' . $e->getMessage());
        }
    }

    /**
     * Show credit purchase form for organization
     */
    public function showOrganizationPurchase(Organization $organization)
    {
        if (!$organization->canUseFeatures()) {
            return redirect()->route('organizations.show', $organization)
                ->with('error', 'Diese Organisation muss erst vom Administrator genehmigt werden, bevor Sie Guthaben aufladen können.');
        }

        $this->authorize('purchaseCredits', $organization);

        $packages = CreditPackage::active()
            ->availableFor($organization)
            ->orderBy('credits', 'asc')
            ->get();

        // Add purchase availability information to packages
        $packages = $packages->map(function ($package) use ($organization) {
            $package->can_be_purchased = $package->canBePurchasedBy($organization);
            $package->remaining_purchases = $package->getRemainingPurchasesFor($organization);
            return $package;
        });

        $balance = $organization->getCurrentCreditBalance();

        return view('credits.purchase.organization', compact('organization', 'packages', 'balance'));
    }

    /**
     * Purchase credits for organization
     */
    public function purchaseOrganizationCredits(Request $request, Organization $organization)
    {
        if (!$organization->canUseFeatures()) {
            return redirect()->route('organizations.show', $organization)
                ->with('error', 'Diese Organisation muss erst vom Administrator genehmigt werden, bevor Sie Guthaben aufladen können.');
        }

        $this->authorize('purchaseCredits', $organization);

        $validated = $request->validate([
            'credit_package_id' => 'required|exists:credit_packages,id',
            'note' => 'nullable|string|max:500',
        ]);

        $package = CreditPackage::findOrFail($validated['credit_package_id']);

        if (!$package->is_active) {
            return back()->with('error', 'Dieses Paket ist nicht mehr verfügbar.');
        }

        try {
            /** @var \App\Models\User $user */
            $user = auth()->user();
            $transaction = $this->creditService->purchaseCredits(
                $organization,
                $package,
                $user,
                $validated['note'] ?? null
            );

            return redirect()->route('credits.organization.transactions', $organization)
                ->with('success', 'Guthaben wurden erfolgreich aufgeladen!');
        } catch (\Exception $e) {
            return back()->with('error', 'Fehler beim Aufladen: ' . $e->getMessage());
        }
    }

    /**
     * Show transfer form
     */
    public function showTransfer(Organization $organization)
    {
        if (!$organization->canUseFeatures()) {
            return redirect()->route('organizations.show', $organization)
                ->with('error', 'Diese Organisation muss erst vom Administrator genehmigt werden, bevor Sie Guthaben umbuchen können.');
        }

        $this->authorize('transferCredits', $organization);

        $facilities = $organization->facilities;
        $balance = $organization->getCurrentCreditBalance();

        return view('credits.transfer', compact('organization', 'facilities', 'balance'));
    }

    /**
     * Transfer credits from organization to facility
     */
    public function transfer(Request $request, Organization $organization)
    {
        if (!$organization->canUseFeatures()) {
            return redirect()->route('organizations.show', $organization)
                ->with('error', 'Diese Organisation muss erst vom Administrator genehmigt werden, bevor Sie Guthaben umbuchen können.');
        }

        $this->authorize('transferCredits', $organization);

        $validated = $request->validate([
            'facility_id' => 'required|exists:facilities,id',
            'amount' => 'required|integer|min:1',
            'note' => 'nullable|string|max:500',
        ]);

        $facility = Facility::findOrFail($validated['facility_id']);

        try {
            /** @var \App\Models\User $user */
            $user = auth()->user();
            $transactions = $this->creditService->transferCredits(
                $organization,
                $facility,
                $validated['amount'],
                $user,
                $validated['note'] ?? null
            );

            return redirect()->route('credits.organization.transactions', $organization)
                ->with('success', 'Guthaben wurden erfolgreich umgebucht!');
        } catch (\Exception $e) {
            return back()->with('error', 'Fehler bei der Umbuchung: ' . $e->getMessage());
        }
    }

    /**
     * Show transfer form for facility to organization
     */
    public function showFacilityTransfer(Facility $facility)
    {
        if (!$facility->organization->canUseFeatures()) {
            return redirect()->route('organizations.show', $facility->organization)
                ->with('error', 'Die Organisation dieser Einrichtung muss erst vom Administrator genehmigt werden, bevor Sie Guthaben umbuchen können.');
        }

        $this->authorize('transferCreditsToOrganization', $facility);

        $organization = $facility->organization;
        $balance = $facility->getCurrentCreditBalance();
        $organizationBalance = $organization->getCurrentCreditBalance();

        return view('credits.transfer-to-organization', compact('facility', 'organization', 'balance', 'organizationBalance'));
    }

    /**
     * Transfer credits from facility to organization
     */
    public function transferToOrganization(Request $request, Facility $facility)
    {
        if (!$facility->organization->canUseFeatures()) {
            return redirect()->route('organizations.show', $facility->organization)
                ->with('error', 'Die Organisation dieser Einrichtung muss erst vom Administrator genehmigt werden, bevor Sie Guthaben umbuchen können.');
        }

        $this->authorize('transferCreditsToOrganization', $facility);

        $validated = $request->validate([
            'amount' => 'required|integer|min:1',
            'note' => 'nullable|string|max:500',
        ]);

        try {
            /** @var \App\Models\User $user */
            $user = auth()->user();
            $transactions = $this->creditService->transferCreditsToOrganization(
                $facility,
                $validated['amount'],
                $user,
                $validated['note'] ?? null
            );

            return redirect()->route('credits.facility.transactions', $facility)
                ->with('success', 'Guthaben wurden erfolgreich an den Träger übertragen!');
        } catch (\Exception $e) {
            return back()->with('error', 'Fehler bei der Übertragung: ' . $e->getMessage());
        }
    }

    /**
     * Show facility transactions
     */
    public function facilityTransactions(Facility $facility)
    {
        if (!$facility->organization->canUseFeatures()) {
            return redirect()->route('organizations.show', $facility->organization)
                ->with('error', 'Die Organisation dieser Einrichtung muss erst vom Administrator genehmigt werden, bevor Sie die Transaktionshistorie einsehen können.');
        }

        $this->authorize('viewTransactions', $facility);

        $transactions = $facility->creditTransactions()->with(['user', 'creditPackage', 'relatedCreditable'])->paginate(20);
        $balance = $facility->getCurrentCreditBalance();

        return view('credits.transactions.facility', compact('facility', 'transactions', 'balance'));
    }

    /**
     * Show organization transactions
     */
    public function organizationTransactions(Organization $organization)
    {
        if (!$organization->canUseFeatures()) {
            return redirect()->route('organizations.show', $organization)
                ->with('error', 'Diese Organisation muss erst vom Administrator genehmigt werden, bevor Sie die Transaktionshistorie einsehen können.');
        }

        $this->authorize('viewTransactions', $organization);

        $transactions = $organization->creditTransactions()->with(['user', 'creditPackage', 'relatedCreditable'])->paginate(20);
        $balance = $organization->getCurrentCreditBalance();

        return view('credits.transactions.organization', compact('organization', 'transactions', 'balance'));
    }
}

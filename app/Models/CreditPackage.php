<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class CreditPackage extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'credits',
        'price',
        'is_active',
        'for_cooperative_members',
        'purchase_limit_per_organization',
    ];

    protected $casts = [
        'credits' => 'integer',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'for_cooperative_members' => 'boolean',
        'purchase_limit_per_organization' => 'integer',
    ];

    /**
     * Get transactions for this package
     */
    public function transactions()
    {
        return $this->hasMany(CreditTransaction::class);
    }

    /**
     * Get price per credit
     */
    public function getPricePerCreditAttribute()
    {
        return $this->credits > 0 ? $this->price / $this->credits : 0;
    }

    /**
     * Scope active packages
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope packages for cooperative members
     */
    public function scopeForCooperativeMembers($query)
    {
        return $query->where('for_cooperative_members', true);
    }

    /**
     * Scope packages for non-cooperative members
     */
    public function scopeForNonCooperativeMembers($query)
    {
        return $query->where('for_cooperative_members', false);
    }

    /**
     * Scope packages available for organization based on their cooperative membership
     */
    public function scopeAvailableFor($query, $organization)
    {
        if ($organization && $organization->is_cooperative_member) {
            return $query->where('for_cooperative_members', true);
        }
        return $query->where('for_cooperative_members', false);
    }

    /**
     * Check if this package has a purchase limit
     */
    public function hasPurchaseLimit(): bool
    {
        return $this->purchase_limit_per_organization !== null && $this->purchase_limit_per_organization > 0;
    }

    /**
     * Get the number of times this package has been purchased by an organization (including its facilities)
     */
    public function getPurchaseCountForOrganization($organization): int
    {
        if (!$organization) {
            return 0;
        }

        // Get organization ID
        $organizationId = $organization instanceof \App\Models\Organization
            ? $organization->id
            : $organization;

        // Count purchases from the organization itself
        $orgPurchases = $this->transactions()
            ->where('type', CreditTransaction::TYPE_PURCHASE)
            ->where('creditable_type', \App\Models\Organization::class)
            ->where('creditable_id', $organizationId)
            ->count();

        // Count purchases from all facilities of this organization
        $facilityIds = \App\Models\Facility::where('organization_id', $organizationId)->pluck('id');
        $facilityPurchases = $this->transactions()
            ->where('type', CreditTransaction::TYPE_PURCHASE)
            ->where('creditable_type', \App\Models\Facility::class)
            ->whereIn('creditable_id', $facilityIds)
            ->count();

        return $orgPurchases + $facilityPurchases;
    }

    /**
     * Check if this package can still be purchased by an organization
     */
    public function canBePurchasedBy($creditable): bool
    {
        // If no limit is set, it can always be purchased
        if (!$this->hasPurchaseLimit()) {
            return true;
        }

        // Get the organization
        $organization = $creditable instanceof \App\Models\Organization
            ? $creditable
            : $creditable->organization;

        if (!$organization) {
            return false;
        }

        // Check if purchase limit has been reached
        $purchaseCount = $this->getPurchaseCountForOrganization($organization);
        return $purchaseCount < $this->purchase_limit_per_organization;
    }

    /**
     * Get remaining purchases available for an organization
     */
    public function getRemainingPurchasesFor($creditable): ?int
    {
        if (!$this->hasPurchaseLimit()) {
            return null; // Unlimited
        }

        // Get the organization
        $organization = $creditable instanceof \App\Models\Organization
            ? $creditable
            : $creditable->organization;

        if (!$organization) {
            return 0;
        }

        $purchaseCount = $this->getPurchaseCountForOrganization($organization);
        $remaining = $this->purchase_limit_per_organization - $purchaseCount;

        return max(0, $remaining);
    }
}

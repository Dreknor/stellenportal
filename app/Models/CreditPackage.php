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
    ];

    protected $casts = [
        'credits' => 'integer',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'for_cooperative_members' => 'boolean',
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
}

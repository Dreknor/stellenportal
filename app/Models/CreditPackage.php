<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class CreditPackage extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'name',
        'description',
        'credits',
        'price',
        'is_active',
    ];

    protected $casts = [
        'credits' => 'integer',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
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
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A batch of credits held by an Organization or Facility.
 *
 * Purchases create lots with an expiry date, transfers move (parts of) lots
 * between entities while keeping the original expiry date, and usage consumes
 * lots FIFO by expiry date. Only the remaining amount of a lot can expire.
 */
class CreditLot extends Model
{
    protected $fillable = [
        'creditable_id',
        'creditable_type',
        'purchase_transaction_id',
        'amount',
        'remaining',
        'expires_at',
    ];

    protected $casts = [
        'amount' => 'integer',
        'remaining' => 'integer',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the owning creditable model (Organization or Facility)
     */
    public function creditable()
    {
        return $this->morphTo();
    }

    /**
     * The purchase this lot originates from (null for admin grants)
     */
    public function purchaseTransaction()
    {
        return $this->belongsTo(CreditTransaction::class, 'purchase_transaction_id');
    }

    /**
     * Scope to lots whose remaining credits are due for expiration
     */
    public function scopeDueForExpiration($query)
    {
        return $query->where('remaining', '>', 0)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now());
    }
}

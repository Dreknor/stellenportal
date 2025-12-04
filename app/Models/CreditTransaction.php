<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class CreditTransaction extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'creditable_id',
        'creditable_type',
        'user_id',
        'credit_package_id',
        'type',
        'amount',
        'balance_after',
        'price_paid',
        'note',
        'related_creditable_id',
        'related_creditable_type',
        'related_transaction_id',
        'expires_at',
    ];

    protected $casts = [
        'amount' => 'integer',
        'balance_after' => 'integer',
        'price_paid' => 'decimal:2',
        'expires_at' => 'datetime',
    ];

    const TYPE_PURCHASE = 'purchase';
    const TYPE_TRANSFER_IN = 'transfer_in';
    const TYPE_TRANSFER_OUT = 'transfer_out';
    const TYPE_USAGE = 'usage';
    const TYPE_ADJUSTMENT = 'adjustment';
    const TYPE_EXPIRATION = 'expiration';

    const EXPIRATION_YEARS = 3;

    /**
     * Check if this transaction is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Scope to get expired purchase transactions that haven't been processed
     */
    public function scopeExpiredPurchases($query)
    {
        return $query->where('type', self::TYPE_PURCHASE)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->whereDoesntHave('expirationTransaction');
    }

    /**
     * Get the expiration transaction for this purchase
     */
    public function expirationTransaction()
    {
        return $this->hasOne(CreditTransaction::class, 'related_transaction_id')
            ->where('type', self::TYPE_EXPIRATION);
    }

    /**
     * Get the owning creditable model (Organization or Facility)
     */
    public function creditable()
    {
        return $this->morphTo();
    }

    /**
     * Alias for creditable (for backwards compatibility)
     */
    public function transactionable()
    {
        return $this->morphTo('creditable');
    }

    /**
     * Get the user who performed the transaction
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the credit package used for purchase
     */
    public function creditPackage()
    {
        return $this->belongsTo(CreditPackage::class);
    }

    /**
     * Get the related creditable (for transfers)
     */
    public function relatedCreditable()
    {
        return $this->morphTo();
    }

    /**
     * Get the related transaction (for transfers)
     */
    public function relatedTransaction()
    {
        return $this->belongsTo(CreditTransaction::class, 'related_transaction_id');
    }
}

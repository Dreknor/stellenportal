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
    ];

    protected $casts = [
        'amount' => 'integer',
        'balance_after' => 'integer',
        'price_paid' => 'decimal:2',
    ];

    const TYPE_PURCHASE = 'purchase';
    const TYPE_TRANSFER_IN = 'transfer_in';
    const TYPE_TRANSFER_OUT = 'transfer_out';
    const TYPE_USAGE = 'usage';
    const TYPE_ADJUSTMENT = 'adjustment';

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

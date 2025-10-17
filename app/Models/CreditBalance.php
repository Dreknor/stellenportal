<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class CreditBalance extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'creditable_id',
        'creditable_type',
        'balance',
    ];

    protected $casts = [
        'balance' => 'integer',
    ];

    /**
     * Get the owning creditable model (Organization or Facility)
     */
    public function creditable()
    {
        return $this->morphTo();
    }

    /**
     * Get all transactions for this balance
     */
    public function transactions()
    {
        return $this->hasMany(CreditTransaction::class, 'creditable_id', 'creditable_id')
            ->where('creditable_type', $this->creditable_type)
            ->orderBy('created_at', 'desc');
    }
}


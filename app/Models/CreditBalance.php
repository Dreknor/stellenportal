<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class CreditBalance extends Model implements Auditable
{
    use HasFactory;
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

    /**
     * Get credits that will expire soon
     * @param int $days Number of days to look ahead
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getExpiringCredits(int $days = 30)
    {
        $expirationDate = now()->addDays($days);

        return CreditTransaction::where('creditable_id', $this->creditable_id)
            ->where('creditable_type', $this->creditable_type)
            ->where('type', CreditTransaction::TYPE_PURCHASE)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', $expirationDate)
            ->whereDoesntHave('expirationTransaction')
            ->orderBy('expires_at', 'asc')
            ->get();
    }

    /**
     * Get total amount of credits expiring soon
     * @param int $days Number of days to look ahead
     * @return int
     */
    public function getExpiringCreditsAmount(int $days = 30): int
    {
        return $this->getExpiringCredits($days)->sum('amount');
    }
}

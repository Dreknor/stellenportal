<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Organization extends Model implements HasMedia
{
    use HasSlug;
    use InteractsWithMedia;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'website',
        'description',
        'slug',
        'is_approved',
        'approved_by',
        'approved_at',
    ];

    public function address()
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function facilities()
    {
        return $this->hasMany(Facility::class);
    }

    /**
     * Get the credit balance for this organization
     */
    public function creditBalance()
    {
        return $this->morphOne(CreditBalance::class, 'creditable');
    }

    /**
     * Get all credit transactions for this organization
     */
    public function creditTransactions()
    {
        return $this->morphMany(CreditTransaction::class, 'creditable')->orderBy('created_at', 'desc');
    }

    /**
     * Get current credit balance amount
     */
    public function getCurrentCreditBalance()
    {
        return $this->creditBalance()->firstOrCreate([])->balance;
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }
}

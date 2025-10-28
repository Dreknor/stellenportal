<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Organization extends Model implements HasMedia, \OwenIt\Auditing\Contracts\Auditable
{
    use HasFactory;
    use HasSlug;
    use InteractsWithMedia;
    use \OwenIt\Auditing\Auditable;

     /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
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

    protected $casts = [
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('header_image')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/jpg']);
    }

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

    /**
     * Get the user who approved this organization
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    /**
     * Check if organization is approved and can use features
     */
    public function canUseFeatures(): bool
    {
        return $this->is_approved;
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

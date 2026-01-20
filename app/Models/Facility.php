<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Facility extends Model implements HasMedia, \OwenIt\Auditing\Contracts\Auditable
{
    use HasFactory;
    use HasSlug;
    use InteractsWithMedia;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'organization_id',
        'name',
        'email',
        'phone',
        'website',
        'description',
        'slug',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('header_image')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/jpg']);

        $this->addMediaCollection('logo')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/jpg']);
    }

    public function address()
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function jobPostings()
    {
        return $this->hasMany(JobPosting::class);
    }

    /**
     * Get the credit balance for this facility
     */
    public function creditBalance()
    {
        return $this->morphOne(CreditBalance::class, 'creditable');
    }

    /**
     * Get all credit transactions for this facility
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

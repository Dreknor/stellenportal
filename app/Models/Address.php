<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[ObservedBy([\App\Observers\AddressObserver::class])]
class Address extends Model implements Auditable, HasMedia
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use InteractsWithMedia;

    protected $fillable = [
        'street',
        'number',
        'city',
        'zip_code',
        'state',
        'latitude',
        'longitude',
        'addressable_id',
        'addressable_type',
    ];

    public function addressable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the state/region, defaulting to Sachsen if not set
     */
    public function getStateOrDefault(): string
    {
        return $this->state ?? 'Sachsen';
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('map')
            ->singleFile();
    }
}

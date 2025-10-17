<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[ObservedBy([\App\Observers\AddressObserver::class])]
class Address extends Model implements Auditable, HasMedia
{
    use \OwenIt\Auditing\Auditable;
    use InteractsWithMedia;

    protected $fillable = [
        'street',
        'number',
        'city',
        'zip_code',
        'latitude',
        'longitude',
    ];

    public function addressable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('map')
            ->singleFile();
    }
}

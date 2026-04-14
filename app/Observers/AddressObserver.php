<?php

namespace App\Observers;

use App\Services\GeocodingService;
use Illuminate\Support\Facades\Log;

class AddressObserver
{
    public function __construct(protected GeocodingService $geocodingService) {}

    /**
     * Handle the Address "creating" event.
     */
    public function creating(\App\Models\Address $address): void
    {
        try {
            $result = $this->geocodingService->geocode($address);

            if ($result) {
                $address->latitude  = $result['lat'];
                $address->longitude = $result['lon'];
            }
        } catch (\Exception $e) {
            Log::error('Geocoding beim Erstellen fehlgeschlagen: ' . $e->getMessage());
        }
    }

    /**
     * Handle the Address "created" event.
     */
    public function created(\App\Models\Address $address): void
    {
        $this->geocodingService->generateMapImage($address);
    }

    /**
     * Handle the Address "updating" event.
     */
    public function updating(\App\Models\Address $address): void
    {
        try {
            $result = $this->geocodingService->geocode($address);

            if ($result) {
                $address->latitude  = $result['lat'];
                $address->longitude = $result['lon'];
            }
        } catch (\Exception $e) {
            Log::error('Geocoding beim Aktualisieren fehlgeschlagen: ' . $e->getMessage());
        }
    }

    /**
     * Handle the Address "updated" event.
     */
    public function updated(\App\Models\Address $address): void
    {
        if ($address->wasChanged(['street', 'number', 'city', 'zip_code', 'latitude', 'longitude'])) {
            $this->geocodingService->generateMapImage($address);
        }
    }
}

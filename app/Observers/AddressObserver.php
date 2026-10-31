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
            // Koordinaten bzw. Fehlergrund werden mit dem Speichern persistiert
            $this->geocodingService->applyResult($address, $this->geocodingService->geocodeWithResult($address));
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
            $this->geocodingService->applyResult($address, $this->geocodingService->geocodeWithResult($address));
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

<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\Facility;
use App\Models\Organization;
use App\Services\GeocodingService;

trait ManagesAddress
{
    /**
     * @return array<string, array<int, string>>
     */
    protected function addressRules(): array
    {
        $required = 'required_with:street,number,zip_code,city,state';

        return [
            'street' => ['nullable', $required, 'string', 'max:255'],
            'number' => ['nullable', $required, 'string', 'max:20'],
            'zip_code' => ['nullable', $required, 'string', 'max:10'],
            'city' => ['nullable', $required, 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Speichert die Adresse; der AddressObserver ermittelt bei Änderungen die Koordinaten neu.
     *
     * @param  array<string, mixed>  $validated
     */
    protected function saveAddress(Organization|Facility $model, array $validated): void
    {
        $data = array_intersect_key($validated, array_flip(['street', 'number', 'zip_code', 'city', 'state']));

        if (empty(array_filter($data))) {
            return;
        }

        $model->address()->updateOrCreate([], $data);
    }

    protected function runGeocoding(Organization|Facility $model, GeocodingService $geocoding)
    {
        $address = $model->address;

        if (! $address) {
            return back()->with('error', 'Es ist keine Adresse hinterlegt.');
        }

        $result = $geocoding->geocode($address);

        if (! $result) {
            return back()->with('error', 'Für diese Adresse konnten keine Koordinaten ermittelt werden.');
        }

        $address->latitude = $result['lat'];
        $address->longitude = $result['lon'];
        $address->saveQuietly();
        $geocoding->generateMapImage($address);

        return back()->with('success', 'Koordinaten erfolgreich ermittelt.');
    }
}

<?php

namespace App\Observers;

use DantSu\OpenStreetMapStaticAPI\LatLng;
use DantSu\OpenStreetMapStaticAPI\Markers;
use DantSu\OpenStreetMapStaticAPI\OpenStreetMap;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AddressObserver
{

    /**
     * Handle the Address "creating" event.
     */
    public function creating(\App\Models\Address $address)
    {
        try {
            $url = config('geocode.geocode_url');
            $key = config('geocode.geocode_key');

            $client = new \GuzzleHttp\Client();

            $street = $address->number . '+' . $address->street;
            $city = $address->city;
            $zip = $address->zip_code;

            $url .= urlencode($street) . '+' . urlencode($city) . '+' . urlencode($zip);
            $url .= '&api_key=' . $key;
            Log::debug("Geocoading URL: " . $url);


            $response = $client->get($url);
            $data = json_decode($response->getBody(), true);
            Log::debug('Geocoding service: ' , [
                'url' => $url,
                'response' => $data,
                'body' => $response->getBody()
            ]);
            if (!empty($data) && isset($data[0]['lat']) && isset($data[0]['lon'])) {
                $address->latitude = $data[0]['lat'];
                $address->longitude = $data[0]['lon'];
            }

        } catch (\Exception $e) {
            Log::error('Geocoding service is not configured properly: ' . $e->getMessage());
            return;
        }
    }

    /**
     * Handle the Address "created" event.
     */
    public function created(\App\Models\Address $address)
    {
        $this->generateMapImage($address);
    }

    /**
     * Handle the Address "updating" event.
     */
    public function updating(\App\Models\Address $address)
    {
        try {
            $url = config('geocode.geocode_url');
            $key = config('geocode.geocode_key');

            $client = new \GuzzleHttp\Client();

            $street = $address->number . '+' . $address->street;
            $city = $address->city;
            $zip = $address->zip_code;

            $url .= urlencode($street) . '+' . urlencode($city) . '+' . urlencode($zip);
            $url .= '&api_key=' . $key;
            Log::debug("Geocoading URL: " . $url);

            $response = $client->get($url);
            $data = json_decode($response->getBody(), true);

            Log::debug('Geocoding service: ' , [
                'url' => $url,
                'response' => $data,
                'body' => $response->getBody()
            ]);

            if (!empty($data) && isset($data[0]['lat']) && isset($data[0]['lon'])) {
                $address->latitude = $data[0]['lat'];
                $address->longitude = $data[0]['lon'];
            }

        } catch (\Exception $e) {
            Log::error('Geocoding service is not configured properly: ' . $e->getMessage());
            return;
        }
    }

    /**
     * Handle the Address "updated" event.
     */
    public function updated(\App\Models\Address $address)
    {
        // Wenn sich relevante Adressdaten geändert haben, neues Kartenbild erstellen
        if ($address->wasChanged(['street', 'number', 'city', 'zip_code', 'latitude', 'longitude'])) {
            $this->generateMapImage($address);
        }
    }

    /**
     * Generiert ein OpenStreetMap-Kartenbild für die Adresse
     */
    protected function generateMapImage(\App\Models\Address $address)
    {
        // Überprüfe ob Koordinaten vorhanden sind
        if (!$address->latitude || !$address->longitude) {
            Log::warning('Cannot generate map: missing coordinates for address ID ' . $address->id);
            return;
        }

        try {
            $api = (new OpenStreetMap(new LatLng($address->latitude, $address->longitude), 17, 600, 400))
                ->addMarkers(
                    (new Markers(public_path('/img/marker.png'), 32, 32))
                        ->setAnchor(Markers::ANCHOR_CENTER, Markers::ANCHOR_BOTTOM)
                        ->addMarker(new LatLng($address->latitude, $address->longitude))
                )
                ->getImage();

            // getImage() gibt ein Image-Objekt zurück, wir benötigen die PNG-Daten
            $image_data = $api->getDataPNG();

            if ($image_data) {
                // Temporäre Datei erstellen
                $tempFilePath = storage_path('app/temp/' . uniqid('map_') . '.png');

                // Stelle sicher, dass das Verzeichnis existiert
                $tempDir = dirname($tempFilePath);
                if (!is_dir($tempDir)) {
                    mkdir($tempDir, 0755, true);
                }

                file_put_contents($tempFilePath, $image_data);

                Log::debug('Map image saved temporarily at: ' . $tempFilePath . ' (Size: ' . strlen($image_data) . ' bytes)');

                // Lösche vorhandenes Kartenbild
                $address->clearMediaCollection('map');

                // Bild zur Media Library hinzufügen
                $address->addMedia($tempFilePath)
                    ->usingFileName($address->id . '_map.png')
                    ->toMediaCollection('map');

                Log::debug('Map was added to media collection for address ID ' . $address->id);

                // Temporäre Datei löschen
                @unlink($tempFilePath);
            }
        } catch (\Exception $e) {
            Log::error('Error generating map image for address ID ' . $address->id . ': ' . $e->getMessage());
        }
    }
}

<?php

namespace App\Services;

use App\Models\Address;
use DantSu\OpenStreetMapStaticAPI\LatLng;
use DantSu\OpenStreetMapStaticAPI\Markers;
use DantSu\OpenStreetMapStaticAPI\OpenStreetMap;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class GeocodingService
{
    /**
     * Versucht, Koordinaten für eine Adresse zu ermitteln.
     *
     * @return array{lat: string, lon: string}|null
     */
    public function geocode(Address $address): ?array
    {
        $url = config('geocode.geocode_url');
        $key = config('geocode.geocode_key');

        $street = $address->number . '+' . $address->street;
        $city   = $address->city;
        $zip    = $address->zip_code;

        $url .= urlencode($street) . '+' . urlencode($city) . '+' . urlencode($zip);
        $url .= '&api_key=' . $key;

        Log::debug('Geocoding URL: ' . $url);

        $client   = new Client();
        $response = $client->get($url);
        $data     = json_decode($response->getBody(), true);

        Log::debug('Geocoding response', [
            'url'      => $url,
            'response' => $data,
        ]);

        if (!empty($data) && isset($data[0]['lat'], $data[0]['lon'])) {
            return [
                'lat' => $data[0]['lat'],
                'lon' => $data[0]['lon'],
            ];
        }

        return null;
    }

    /**
     * Generiert ein OpenStreetMap-Kartenbild für eine Adresse und speichert es
     * in der Media Library (Collection "map").
     */
    public function generateMapImage(Address $address): void
    {
        if (!$address->latitude || !$address->longitude) {
            Log::warning('Kartenbild kann nicht erstellt werden: Koordinaten fehlen für Adresse ID ' . $address->id);

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

            $imageData = $api->getDataPNG();

            if ($imageData) {
                $tempFilePath = storage_path('app/temp/' . uniqid('map_') . '.png');
                $tempDir      = dirname($tempFilePath);

                if (!is_dir($tempDir)) {
                    mkdir($tempDir, 0755, true);
                }

                file_put_contents($tempFilePath, $imageData);

                Log::debug('Kartenbild temporär gespeichert: ' . $tempFilePath . ' (Größe: ' . strlen($imageData) . ' Bytes)');

                $address->clearMediaCollection('map');
                $address->addMedia($tempFilePath)
                    ->usingFileName($address->id . '_map.png')
                    ->toMediaCollection('map');

                Log::debug('Kartenbild zur Media Library hinzugefügt für Adresse ID ' . $address->id);

                @unlink($tempFilePath);
            }
        } catch (\Exception $e) {
            Log::error('Fehler beim Erstellen des Kartenbildes für Adresse ID ' . $address->id . ': ' . $e->getMessage());
        }
    }
}


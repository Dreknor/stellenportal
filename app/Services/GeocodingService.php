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
     * Probiert bei Misserfolg automatisch mehrere Adressvarianten (Fallbacks).
     *
     * @return array{lat: string, lon: string}|null
     */
    public function geocode(Address $address): ?array
    {
        $variants = $this->buildAddressVariants($address);

        foreach ($variants as $variant) {
            $result = $this->fetchCoordinates(
                $variant['street'],
                $variant['city'],
                $variant['zip'],
                $variant['label']
            );

            if ($result !== null) {
                return $result;
            }
        }

        Log::warning('Geocoding: Alle Adressvarianten erschöpft – keine Koordinaten gefunden.', [
            'address_id' => $address->id,
            'city'       => $address->city,
        ]);

        return null;
    }

    /**
     * Baut eine Liste von Adressvarianten, die nacheinander ausprobiert werden.
     *
     * Berücksichtigt Ortsteil-Muster wie:
     *   - "Grimma - OT Großbardau"          → Stadt: "Grimma",  Ortsteil: "Großbardau"
     *   - "Reichenbach im Vogtl. OT Mylau"  → Stadt: "Reichenbach im Vogtl.", Ortsteil: "Mylau"
     *   - "Wilsdruff OT Grumbach"           → Stadt: "Wilsdruff", Ortsteil: "Grumbach"
     *
     * @return array<int, array{street: string, city: string, zip: string, label: string}>
     */
    protected function buildAddressVariants(Address $address): array
    {
        $street   = trim($address->number . ' ' . $address->street);
        $city     = $address->city ?? '';
        $zip      = $address->zip_code ?? '';

        $variants = [];

        // 1. Original-Adresse
        $variants[] = [
            'street' => $street,
            'city'   => $city,
            'zip'    => $zip,
            'label'  => 'Original',
        ];

        // 2. Ortsteil-Extraktion  (Muster: " - OT " oder " OT ")
        $mainCity    = null;
        $districtCity = null;

        if (preg_match('/^(.+?)\s*-\s*OT\s+(.+)$/i', $city, $matches)) {
            // "Grimma - OT Großbardau"
            $mainCity     = trim($matches[1]);
            $districtCity = trim($matches[2]);
        } elseif (preg_match('/^(.+?)\s+OT\s+(.+)$/i', $city, $matches)) {
            // "Reichenbach im Vogtl. OT Mylau" / "Wilsdruff OT Grumbach"
            $mainCity     = trim($matches[1]);
            $districtCity = trim($matches[2]);
        }

        if ($mainCity !== null) {
            // 2a. Nur die Hauptstadt (ohne OT-Suffix)
            $variants[] = [
                'street' => $street,
                'city'   => $mainCity,
                'zip'    => $zip,
                'label'  => "Hauptstadt ({$mainCity})",
            ];

            // 2b. Nur den Ortsteil als Stadt
            $variants[] = [
                'street' => $street,
                'city'   => $districtCity,
                'zip'    => $zip,
                'label'  => "Ortsteil ({$districtCity})",
            ];

            // 2c. Ortsteil mit PLZ, ohne Straße (sehr reduziert)
            $variants[] = [
                'street' => '',
                'city'   => $districtCity,
                'zip'    => $zip,
                'label'  => "Nur Ortsteil + PLZ ({$districtCity}, {$zip})",
            ];
        }

        // 3. Nur PLZ + Straße (ohne Stadtname) – universeller Fallback
        $variants[] = [
            'street' => $street,
            'city'   => '',
            'zip'    => $zip,
            'label'  => 'Nur Straße + PLZ',
        ];

        return $variants;
    }

    /**
     * Führt den eigentlichen HTTP-Request an den Geocodierungs-Dienst durch.
     *
     * @return array{lat: string, lon: string}|null
     */
    protected function fetchCoordinates(string $street, string $city, string $zip, string $variantLabel): ?array
    {
        $baseUrl = config('geocode.geocode_url');
        $key     = config('geocode.geocode_key');

        $parts = array_filter([
            str_replace(' ', '+', trim($street)),
            str_replace(' ', '+', trim($city)),
            str_replace(' ', '+', trim($zip)),
        ]);

        $query = implode('+', $parts);
        $url   = $baseUrl . urlencode($query) . '&api_key=' . $key;

        Log::debug("Geocoding [{$variantLabel}]: {$url}");

        try {
            $client   = new Client();
            $response = $client->get($url);
            $data     = json_decode($response->getBody(), true);

            if (!empty($data) && isset($data[0]['lat'], $data[0]['lon'])) {
                Log::debug("Geocoding [{$variantLabel}]: Koordinaten gefunden.", [
                    'lat' => $data[0]['lat'],
                    'lon' => $data[0]['lon'],
                ]);

                return [
                    'lat' => $data[0]['lat'],
                    'lon' => $data[0]['lon'],
                ];
            }

            Log::debug("Geocoding [{$variantLabel}]: Keine Ergebnisse.");
        } catch (\Exception $e) {
            Log::error("Geocoding [{$variantLabel}]: HTTP-Fehler – " . $e->getMessage());
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


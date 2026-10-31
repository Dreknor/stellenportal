<?php

namespace App\Services;

use App\Models\Address;
use App\Services\Geocoding\GeocodingException;
use App\Services\Geocoding\GeocodingFailureReason;
use App\Services\Geocoding\GeocodingResult;
use DantSu\OpenStreetMapStaticAPI\LatLng;
use DantSu\OpenStreetMapStaticAPI\Markers;
use DantSu\OpenStreetMapStaticAPI\OpenStreetMap;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeocodingService
{
    /**
     * Versucht, Koordinaten für eine Adresse zu ermitteln.
     * Probiert bei Misserfolg automatisch mehrere Adressvarianten (Fallbacks).
     *
     * Den Fehlergrund liefert geocodeWithResult().
     *
     * @return array{lat: string, lon: string}|null
     */
    public function geocode(Address $address): ?array
    {
        return $this->geocodeWithResult($address)->coordinates();
    }

    /**
     * Wie geocode(), liefert aber zusätzlich den Fehlergrund und alle probierten Varianten.
     */
    public function geocodeWithResult(Address $address): GeocodingResult
    {
        if (blank($address->zip_code) && blank($address->city)) {
            return $this->failed($address, GeocodingFailureReason::IncompleteAddress, []);
        }

        $attempts = [];

        foreach ($this->buildAddressVariants($address) as $variant) {
            try {
                $result = $this->fetchCoordinates(
                    $variant['street'],
                    $variant['city'],
                    $variant['zip'],
                    $variant['label']
                );
            } catch (GeocodingException $e) {
                $attempts[] = ['label' => $variant['label'], 'reason' => $e->reason, 'detail' => $e->detail];

                if ($e->reason->abortsRemainingVariants()) {
                    return $this->failed($address, $e->reason, $attempts);
                }

                continue;
            }

            if ($result !== null) {
                $attempts[] = ['label' => $variant['label'], 'reason' => null, 'detail' => null];

                return GeocodingResult::success($result['lat'], $result['lon'], $attempts);
            }

            $attempts[] = ['label' => $variant['label'], 'reason' => GeocodingFailureReason::NoResults, 'detail' => null];
        }

        // Ein technischer Fehler wiegt schwerer als "keine Treffer", da er einen Treffer verdeckt haben könnte.
        $reason = GeocodingFailureReason::NoResults;
        foreach ($attempts as $attempt) {
            if ($attempt['reason'] !== GeocodingFailureReason::NoResults) {
                $reason = $attempt['reason'];
                break;
            }
        }

        return $this->failed($address, $reason, $attempts);
    }

    /**
     * Übernimmt das Ergebnis in die Adresse (ohne zu speichern):
     * bei Erfolg die Koordinaten (Fehler wird zurückgesetzt), sonst den Fehlergrund.
     */
    public function applyResult(Address $address, GeocodingResult $result): void
    {
        if ($result->successful()) {
            // forceFill: Koordinaten als String übernehmen, um Präzisionsverlust zu vermeiden
            $address->forceFill([
                'latitude'            => $result->latitude,
                'longitude'           => $result->longitude,
                'geocoding_error'     => null,
                'geocoding_failed_at' => null,
            ]);

            return;
        }

        $address->geocoding_error = $result->message();
        $address->geocoding_failed_at = now();
    }

    /**
     * @param  array<int, array{label: string, reason: GeocodingFailureReason|null, detail: string|null}>  $attempts
     */
    protected function failed(Address $address, GeocodingFailureReason $reason, array $attempts): GeocodingResult
    {
        Log::warning('Geocoding: Keine Koordinaten gefunden.', [
            'address_id' => $address->id,
            'city'       => $address->city,
            'reason'     => $reason->value,
        ]);

        return GeocodingResult::failure($reason, $attempts);
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
     * @return array{lat: string, lon: string}|null null, wenn der Dienst keine Treffer liefert
     *
     * @throws GeocodingException wenn die Anfrage technisch scheitert
     */
    protected function fetchCoordinates(string $street, string $city, string $zip, string $variantLabel): ?array
    {
        $baseUrl = (string) config('geocode.geocode_url');
        $key     = (string) config('geocode.geocode_key');

        if ($baseUrl === '' || $key === '') {
            throw new GeocodingException(GeocodingFailureReason::NotConfigured);
        }

        $parts = array_filter([
            str_replace(' ', '+', trim($street)),
            str_replace(' ', '+', trim($city)),
            str_replace(' ', '+', trim($zip)),
        ]);

        $query = implode('+', $parts);
        $url   = $baseUrl . urlencode($query) . '&api_key=';

        // API-Schlüssel niemals loggen
        Log::debug("Geocoding [{$variantLabel}]: {$url}***");

        try {
            $response = Http::timeout(10)->get($url . $key);
        } catch (ConnectionException $e) {
            Log::error("Geocoding [{$variantLabel}]: Verbindungsfehler – " . $this->maskKey($e->getMessage(), $key));

            throw new GeocodingException(GeocodingFailureReason::ConnectionFailed, previous: $e);
        }

        if ($response->failed()) {
            $status = $response->status();
            $reason = match (true) {
                in_array($status, [401, 403], true) => GeocodingFailureReason::InvalidApiKey,
                $status === 429                     => GeocodingFailureReason::RateLimited,
                default                             => GeocodingFailureReason::ServiceError,
            };

            Log::error("Geocoding [{$variantLabel}]: HTTP-Fehler {$status}");

            throw new GeocodingException($reason, "HTTP {$status}");
        }

        $data = json_decode($response->body(), true);

        if (! is_array($data)) {
            Log::error("Geocoding [{$variantLabel}]: Ungültige Antwort (kein JSON-Array).");

            throw new GeocodingException(GeocodingFailureReason::InvalidResponse);
        }

        if (isset($data[0]['lat'], $data[0]['lon'])) {
            Log::debug("Geocoding [{$variantLabel}]: Koordinaten gefunden.", [
                'lat' => $data[0]['lat'],
                'lon' => $data[0]['lon'],
            ]);

            return [
                'lat' => (string) $data[0]['lat'],
                'lon' => (string) $data[0]['lon'],
            ];
        }

        Log::debug("Geocoding [{$variantLabel}]: Keine Ergebnisse.");

        return null;
    }

    /**
     * Entfernt den API-Schlüssel aus Texten (z. B. Exception-Meldungen, die die URL enthalten).
     */
    protected function maskKey(string $text, string $key): string
    {
        return $key === '' ? $text : str_replace([$key, urlencode($key)], '***', $text);
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
            // Das dantsu/php-image-editor-Paket liest $_SERVER["REQUEST_SCHEME"] u. a. direkt
            // aus, um einen cURL-Referer zu bauen. Im CLI-Kontext (Artisan-Commands, Cron-Jobs)
            // sind diese Variablen nicht gesetzt → "Undefined array key" Fehler.
            // Wir befüllen sie mit Fallback-Werten, sofern sie fehlen.
            if (!isset($_SERVER['REQUEST_SCHEME'])) {
                $_SERVER['REQUEST_SCHEME'] = 'https';
            }
            if (!isset($_SERVER['HTTP_HOST'])) {
                $_SERVER['HTTP_HOST'] = parse_url(config('app.url'), PHP_URL_HOST) ?? 'localhost';
            }
            if (!isset($_SERVER['REQUEST_URI'])) {
                $_SERVER['REQUEST_URI'] = '/';
            }

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


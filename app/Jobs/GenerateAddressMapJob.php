<?php

namespace App\Jobs;

use App\Models\Address;
use DantSu\OpenStreetMapStaticAPI\LatLng;
use DantSu\OpenStreetMapStaticAPI\Markers;
use DantSu\OpenStreetMapStaticAPI\OpenStreetMap;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateAddressMapJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /** Anzahl maximaler Versuche. */
    public int $tries = 3;

    /** Maximallaufzeit in Sekunden. */
    public int $timeout = 60;

    /** Backoff-Strategie zwischen Retries. */
    public function backoff(): array
    {
        return [30, 120, 300];
    }

    public function __construct(public int $addressId)
    {
    }

    public function handle(): void
    {
        /** @var Address|null $address */
        $address = Address::find($this->addressId);

        if (!$address) {
            return;
        }

        if (!$address->latitude || !$address->longitude) {
            Log::warning('GenerateAddressMapJob: fehlende Koordinaten', ['address_id' => $address->id]);
            return;
        }

        try {
            $width = (int) config('geocode.map_width', 600);
            $height = (int) config('geocode.map_height', 400);
            $zoom = (int) config('geocode.map_zoom', 17);

            $api = (new OpenStreetMap(new LatLng($address->latitude, $address->longitude), $zoom, $width, $height))
                ->addMarkers(
                    (new Markers(public_path('/img/marker.png'), 32, 32))
                        ->setAnchor(Markers::ANCHOR_CENTER, Markers::ANCHOR_BOTTOM)
                        ->addMarker(new LatLng($address->latitude, $address->longitude))
                )
                ->getImage();

            $imageData = $api->getDataPNG();

            if (!$imageData) {
                return;
            }

            $tempDir = storage_path('app/temp');
            if (!is_dir($tempDir)) {
                @mkdir($tempDir, 0755, true);
            }

            $tempFilePath = $tempDir . DIRECTORY_SEPARATOR . uniqid('map_', true) . '.png';

            try {
                file_put_contents($tempFilePath, $imageData);

                $address->clearMediaCollection('map');
                $address->addMedia($tempFilePath)
                    ->usingFileName($address->id . '_map.png')
                    ->toMediaCollection('map');
            } finally {
                // Garbage Collection der temporären Datei, auch bei Fehlern.
                if (is_file($tempFilePath)) {
                    @unlink($tempFilePath);
                }
            }
        } catch (\Throwable $e) {
            Log::error('GenerateAddressMapJob: Rendering fehlgeschlagen', [
                'address_id' => $address->id,
                'error' => $e->getMessage(),
            ]);
            throw $e; // Retry gemäß $tries/$backoff
        }
    }
}


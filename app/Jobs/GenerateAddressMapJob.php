<?php

namespace App\Jobs;

use App\Models\Address;
use App\Services\GeocodingService;
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

        // Shared implementation (also prepares the CLI context for the tile library)
        app(GeocodingService::class)->generateMapImage($address);
    }
}


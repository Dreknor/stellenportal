<?php

namespace App\Console\Commands;

use App\Mail\GeocodingFailedMail;
use App\Models\Address;
use App\Models\User;
use App\Services\GeocodingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class GeocodeAddresses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'addresses:geocode
                            {--limit=100 : Maximale Anzahl zu verarbeitender Adressen pro Durchlauf}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ermittelt fehlende Koordinaten für Adressen und benachrichtigt Admins bei Fehlern';

    public function __construct(protected GeocodingService $geocodingService)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $limit = (int) $this->option('limit');

        $addresses = Address::whereNull('latitude')
            ->orWhereNull('longitude')
            ->limit($limit)
            ->get();

        if ($addresses->isEmpty()) {
            $this->info('Alle Adressen haben bereits Koordinaten. Nichts zu tun.');

            return Command::SUCCESS;
        }

        $this->info("Gefundene Adressen ohne Koordinaten: {$addresses->count()}");

        $successCount = 0;
        $failedAddresses = [];

        foreach ($addresses as $address) {
            $label = sprintf(
                '%s %s, %s %s (ID: %d)',
                $address->street,
                $address->number,
                $address->zip_code,
                $address->city,
                $address->id
            );

            try {
                $result = $this->geocodingService->geocode($address);

                if ($result) {
                    // saveQuietly() umgeht alle Observer/Events, verhindert so
                    // doppeltes Geocoding durch den AddressObserver
                    $address->latitude  = $result['lat'];
                    $address->longitude = $result['lon'];
                    $address->saveQuietly();

                    // Kartenbild manuell generieren, da der Observer umgangen wurde
                    $this->geocodingService->generateMapImage($address);

                    $this->info("✓ Koordinaten gefunden für: {$label}");
                    $successCount++;
                } else {
                    $this->warn("✗ Keine Koordinaten gefunden für: {$label}");
                    $failedAddresses[] = $address;

                    Log::warning('Geocoding fehlgeschlagen: Keine Ergebnisse', [
                        'address_id' => $address->id,
                        'address'    => $label,
                    ]);
                }
            } catch (\Exception $e) {
                $this->error("✗ Fehler bei der Geocodierung von: {$label} – {$e->getMessage()}");
                $failedAddresses[] = $address;

                Log::error('Geocoding-Fehler', [
                    'address_id' => $address->id,
                    'address'    => $label,
                    'error'      => $e->getMessage(),
                ]);
            }
        }

        $this->info("Geocodierung abgeschlossen: {$successCount} erfolgreich, " . count($failedAddresses) . ' fehlgeschlagen.');

        if (!empty($failedAddresses)) {
            $this->notifyAdmins($failedAddresses);
        }

        return Command::SUCCESS;
    }

    /**
     * Benachrichtigt alle Admins mit der Berechtigung "admin edit organizations" über
     * Adressen, für die keine Koordinaten ermittelt werden konnten.
     *
     * @param  Address[]  $failedAddresses
     */
    protected function notifyAdmins(array $failedAddresses): void
    {
        $admins = User::permission('admin edit organizations')->get();

        if ($admins->isEmpty()) {
            $this->warn('Keine Admins gefunden, um die Fehlermeldung zu versenden.');

            return;
        }

        foreach ($admins as $admin) {
            try {
                Mail::to($admin->email)->queue(new GeocodingFailedMail($failedAddresses));
                $this->info("Admin-Benachrichtigung versendet an: {$admin->email}");
            } catch (\Exception $e) {
                $this->error("Fehler beim Versenden der Benachrichtigung an {$admin->email}: {$e->getMessage()}");
                Log::error('Fehler beim Versenden der Geocoding-Fehlermeldung', [
                    'admin_id' => $admin->id,
                    'error'    => $e->getMessage(),
                ]);
            }
        }
    }
}


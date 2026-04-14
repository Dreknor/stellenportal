<?php

namespace App\Console\Commands;

use App\Mail\MapImageFailedMail;
use App\Models\Address;
use App\Models\User;
use App\Services\GeocodingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class GenerateMapImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'addresses:generate-maps
                            {--limit=100 : Maximale Anzahl zu verarbeitender Adressen pro Durchlauf}
                            {--force : Auch Adressen mit vorhandenem Kartenbild neu generieren}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generiert fehlende Kartenausschnitte (map-Bilder) für Adressen mit vorhandenen Koordinaten';

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
        $force = (bool) $this->option('force');

        $query = Address::whereNotNull('latitude')
            ->whereNotNull('longitude');

        if (!$force) {
            // Nur Adressen, die noch kein Bild in der "map"-Collection haben
            $query->whereDoesntHave('media', function ($q) {
                $q->where('collection_name', 'map');
            });
        }

        $addresses = $query->limit($limit)->get();

        if ($addresses->isEmpty()) {
            $this->info('Alle Adressen mit Koordinaten haben bereits ein Kartenbild. Nichts zu tun.');

            return Command::SUCCESS;
        }

        $this->info("Gefundene Adressen ohne Kartenbild: {$addresses->count()}");

        $successCount   = 0;
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
                $this->geocodingService->generateMapImage($address);

                // Prüfen, ob tatsächlich ein Bild angelegt wurde
                $address->refresh();

                if ($address->getFirstMedia('map')) {
                    $this->info("✓ Kartenbild erstellt für: {$label}");
                    $successCount++;
                } else {
                    $this->warn("✗ Kein Kartenbild erzeugt für: {$label} (API lieferte keine Bilddaten)");
                    $failedAddresses[] = ['address' => $address, 'error' => 'Keine Bilddaten von der API erhalten'];

                    Log::warning('Kartenbild-Generierung: keine Bilddaten erhalten', [
                        'address_id' => $address->id,
                        'address'    => $label,
                    ]);
                }
            } catch (\Exception $e) {
                $this->error("✗ Fehler beim Erstellen des Kartenbildes für: {$label} – {$e->getMessage()}");
                $failedAddresses[] = ['address' => $address, 'error' => $e->getMessage()];

                Log::error('Kartenbild-Generierung fehlgeschlagen', [
                    'address_id' => $address->id,
                    'address'    => $label,
                    'error'      => $e->getMessage(),
                ]);
            }
        }

        $this->info("Kartenbild-Generierung abgeschlossen: {$successCount} erfolgreich, " . count($failedAddresses) . ' fehlgeschlagen.');

        if (!empty($failedAddresses)) {
            $this->notifyAdmins($failedAddresses);
        }

        return Command::SUCCESS;
    }

    /**
     * Benachrichtigt alle Admins mit der Berechtigung "admin edit organizations" über
     * Adressen, für die kein Kartenbild erstellt werden konnte.
     *
     * @param  array<int, array{address: Address, error: string}>  $failedAddresses
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
                Mail::to($admin->email)->queue(new MapImageFailedMail($failedAddresses));
                $this->info("Admin-Benachrichtigung versendet an: {$admin->email}");
            } catch (\Exception $e) {
                $this->error("Fehler beim Versenden der Benachrichtigung an {$admin->email}: {$e->getMessage()}");
                Log::error('Fehler beim Versenden der Kartenbild-Fehlermeldung', [
                    'admin_id' => $admin->id,
                    'error'    => $e->getMessage(),
                ]);
            }
        }
    }
}


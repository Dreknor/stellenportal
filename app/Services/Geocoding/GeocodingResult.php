<?php

namespace App\Services\Geocoding;

/**
 * Ergebnis eines Geocodierungs-Versuchs inkl. aller probierten Adressvarianten.
 */
final class GeocodingResult
{
    /**
     * @param  array<int, array{label: string, reason: GeocodingFailureReason|null, detail: string|null}>  $attempts
     */
    private function __construct(
        public readonly ?string $latitude,
        public readonly ?string $longitude,
        public readonly ?GeocodingFailureReason $reason,
        public readonly array $attempts,
    ) {}

    /**
     * @param  array<int, array{label: string, reason: GeocodingFailureReason|null, detail: string|null}>  $attempts
     */
    public static function success(string $latitude, string $longitude, array $attempts = []): self
    {
        return new self($latitude, $longitude, null, $attempts);
    }

    /**
     * @param  array<int, array{label: string, reason: GeocodingFailureReason|null, detail: string|null}>  $attempts
     */
    public static function failure(GeocodingFailureReason $reason, array $attempts = []): self
    {
        return new self(null, null, $reason, $attempts);
    }

    public function successful(): bool
    {
        return $this->reason === null;
    }

    /**
     * @return array{lat: string, lon: string}|null
     */
    public function coordinates(): ?array
    {
        if ($this->latitude === null || $this->longitude === null) {
            return null;
        }

        return ['lat' => $this->latitude, 'lon' => $this->longitude];
    }

    /**
     * Nutzerverständliche Fehlermeldung inkl. der probierten Varianten, z. B.
     * "Der Geocodierungs-Dienst hat … keine Treffer gefunden. (Versucht: Original, Nur Straße + PLZ)".
     */
    public function message(): ?string
    {
        if ($this->reason === null) {
            return null;
        }

        $message = $this->reason->label();

        foreach ($this->attempts as $attempt) {
            if ($attempt['reason'] === $this->reason && $attempt['detail'] !== null) {
                $message .= ' (' . $attempt['detail'] . ')';
                break;
            }
        }

        $labels = array_column($this->attempts, 'label');

        if ($labels !== []) {
            $message .= ' Versucht: ' . implode(', ', $labels) . '.';
        }

        return $message;
    }
}

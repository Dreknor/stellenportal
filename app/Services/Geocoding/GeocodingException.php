<?php

namespace App\Services\Geocoding;

use RuntimeException;

/**
 * Wird von GeocodingService::fetchCoordinates() geworfen, wenn eine Anfrage
 * nicht nur "keine Treffer" lieferte, sondern technisch gescheitert ist.
 */
class GeocodingException extends RuntimeException
{
    /**
     * @param  string|null  $detail  Technisches Detail für Nutzer (z. B. "HTTP 500"); niemals den API-Schlüssel enthalten
     */
    public function __construct(
        public readonly GeocodingFailureReason $reason,
        public readonly ?string $detail = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($reason->label() . ($detail !== null ? " ({$detail})" : ''), 0, $previous);
    }
}

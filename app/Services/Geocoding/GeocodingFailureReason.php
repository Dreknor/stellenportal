<?php

namespace App\Services\Geocoding;

/**
 * Mögliche Gründe, warum für eine Adresse keine Koordinaten ermittelt werden konnten.
 */
enum GeocodingFailureReason: string
{
    case NotConfigured = 'not_configured';
    case IncompleteAddress = 'incomplete_address';
    case NoResults = 'no_results';
    case InvalidApiKey = 'invalid_api_key';
    case RateLimited = 'rate_limited';
    case ServiceError = 'service_error';
    case ConnectionFailed = 'connection_failed';
    case InvalidResponse = 'invalid_response';

    /**
     * Nutzerverständliche Beschreibung (deutsch).
     */
    public function label(): string
    {
        return match ($this) {
            self::NotConfigured => 'Der Geocodierungs-Dienst ist nicht konfiguriert (URL oder API-Schlüssel fehlt).',
            self::IncompleteAddress => 'Die Adresse ist unvollständig – es fehlen PLZ und Ort.',
            self::NoResults => 'Der Geocodierungs-Dienst hat zu dieser Adresse keine Treffer gefunden. Bitte Schreibweise von Straße, PLZ und Ort prüfen.',
            self::InvalidApiKey => 'Der Geocodierungs-Dienst hat die Anfrage abgelehnt – der API-Schlüssel ist ungültig oder abgelaufen.',
            self::RateLimited => 'Das Anfragelimit des Geocodierungs-Dienstes ist erreicht. Bitte später erneut versuchen.',
            self::ServiceError => 'Der Geocodierungs-Dienst hat mit einem Fehler geantwortet.',
            self::ConnectionFailed => 'Der Geocodierungs-Dienst ist nicht erreichbar (Zeitüberschreitung oder Verbindungsfehler).',
            self::InvalidResponse => 'Der Geocodierungs-Dienst hat eine ungültige Antwort geliefert.',
        };
    }

    /**
     * Bei diesen Gründen ist es zwecklos, weitere Adressvarianten zu probieren,
     * weil jede Anfrage mit derselben Ursache scheitern würde.
     */
    public function abortsRemainingVariants(): bool
    {
        return in_array($this, [self::NotConfigured, self::InvalidApiKey, self::RateLimited], true);
    }
}

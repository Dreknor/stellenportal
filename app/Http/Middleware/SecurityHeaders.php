<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Setzt eine Basis-Ausstattung an Security-Headern auf jeder HTTP-Antwort.
 *
 * Hinweis: Die Content-Security-Policy ist bewusst moderat gehalten, um mit
 * TinyMCE, Alpine.js, Blade-Icons und OpenStreetMap-Kachelbildern kompatibel
 * zu bleiben. Für eine strengere CSP müssten Inline-Skripte / Inline-Styles
 * mit Nonces versehen werden.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        // Nur HTML-Responses erhalten CSP & Co.; APIs/JSON/PDF bleiben unberührt
        // bis auf die ungefährlichen Basis-Header.
        $headers = $response->headers;

        if (!$headers->has('X-Content-Type-Options')) {
            $headers->set('X-Content-Type-Options', 'nosniff');
        }

        if (!$headers->has('X-Frame-Options')) {
            $headers->set('X-Frame-Options', 'SAMEORIGIN');
        }

        if (!$headers->has('Referrer-Policy')) {
            $headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        }

        if (!$headers->has('Permissions-Policy')) {
            $headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(self)');
        }

        // HSTS nur über HTTPS sinnvoll.
        if ($request->isSecure() && !$headers->has('Strict-Transport-Security')) {
            $headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // CSP nur auf HTML-Antworten, damit z. B. PDF-Downloads unbeeinträchtigt bleiben.
        $contentType = (string) $headers->get('Content-Type', '');
        $isHtml = $contentType === '' || str_contains($contentType, 'text/html');

        if ($isHtml && !$headers->has('Content-Security-Policy')) {
            $headers->set('Content-Security-Policy', $this->buildContentSecurityPolicy());
        }

        return $response;
    }

    protected function buildContentSecurityPolicy(): string
    {
        // TinyMCE + Alpine.js erfordern derzeit 'unsafe-inline' und 'unsafe-eval'.
        // Bilder: OSM-Kacheln (*.openstreetmap.org / tile.openstreetmap.org),
        //         Geocoding-Provider sowie eigene Media-Library-Uploads.
        $directives = [
            "default-src 'self'",
            "base-uri 'self'",
            "object-src 'none'",
            "frame-ancestors 'self'",
            "form-action 'self'",
            "img-src 'self' data: blob: https:",
            "font-src 'self' data: https:",
            "style-src 'self' 'unsafe-inline' https:",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https:",
            "connect-src 'self' https:",
        ];

        return implode('; ', $directives);
    }
}


<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LocationSuggestionsController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $q = trim((string) $request->get('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        try {
            $url = config('geocode.geocode_url');
            $key = config('geocode.geocode_key');

            if (!$url || !$key) {
                return response()->json([]);
            }

            $client     = new Client();
            $requestUrl = $url
                . urlencode($q)
                . '&api_key=' . $key
                . '&countrycodes=de'
                . '&addressdetails=1'
                . '&limit=7';

            $response = $client->get($requestUrl, ['timeout' => 5]);
            $data     = json_decode($response->getBody(), true);

            if (empty($data) || !is_array($data)) {
                return response()->json([]);
            }

            $suggestions = collect($data)
                ->map(fn (array $item) => [
                    'display_name' => $item['display_name'] ?? '',
                    'short_name'   => $this->buildShortName($item),
                    'lat'          => (string) ($item['lat'] ?? ''),
                    'lon'          => (string) ($item['lon'] ?? ''),
                ])
                ->filter(fn ($s) => $s['lat'] !== '' && $s['lon'] !== '')
                ->values();

            return response()->json($suggestions);
        } catch (\Exception $e) {
            Log::warning('Ortsvorschläge konnten nicht geladen werden: ' . $e->getMessage());

            return response()->json([]);
        }
    }

    /**
     * Baut einen kompakten Anzeigetext wie "Neustadt, Sachsen (01844)" aus
     * einem Nominatim-Ergebnis zusammen.
     */
    private function buildShortName(array $item): string
    {
        $address = $item['address'] ?? [];

        // Ortsname – Nominatim liefert ihn meistens als city/town/village/hamlet
        $place = $address['city']
            ?? $address['town']
            ?? $address['village']
            ?? $address['hamlet']
            ?? $address['municipality']
            ?? null;

        if ($place === null) {
            // Fallback: erstes Segment des display_name
            $place = explode(',', $item['display_name'] ?? '')[0];
        }

        $parts = [trim($place)];

        // Bundesland
        if (!empty($address['state'])) {
            $parts[] = $address['state'];
        } elseif (!empty($address['county'])) {
            $parts[] = $address['county'];
        }

        // PLZ
        if (!empty($address['postcode'])) {
            $parts[] = $address['postcode'];
        }

        return implode(', ', array_filter(array_unique($parts)));
    }
}


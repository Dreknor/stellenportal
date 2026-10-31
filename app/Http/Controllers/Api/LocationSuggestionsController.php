<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class LocationSuggestionsController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $q = trim((string) $request->get('q', ''));

        if (mb_strlen($q) < 2 || mb_strlen($q) > 100) {
            return response()->json([]);
        }

        return response()->json(
            Cache::remember('location-suggestions:' . md5(mb_strtolower($q)), now()->addDay(), fn () => $this->fetchSuggestions($q))
        );
    }

    /**
     * @return array<int, array{display_name: string, short_name: string, lat: string, lon: string}>
     */
    private function fetchSuggestions(string $q): array
    {
        $key = config('geocode.geocode_key');

        try {
            $url = config('geocode.geocode_url');

            if (!$url || !$key) {
                return [];
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
                return [];
            }

            $suggestions = collect($data)
                ->map(fn (array $item) => [
                    'display_name' => $item['display_name'] ?? '',
                    'short_name'   => $this->buildShortName($item),
                    'lat'          => (string) ($item['lat'] ?? ''),
                    'lon'          => (string) ($item['lon'] ?? ''),
                ])
                ->filter(fn ($s) => $s['lat'] !== '' && $s['lon'] !== '')
                ->values()
                ->all();

            return $suggestions;
        } catch (\Exception $e) {
            // The request URL contains the API key, never log it unmasked
            Log::warning('Ortsvorschläge konnten nicht geladen werden: ' . str_replace((string) $key, '***', $e->getMessage()));

            return [];
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


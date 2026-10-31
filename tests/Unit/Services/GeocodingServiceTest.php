<?php

namespace Tests\Unit\Services;

use App\Models\Address;
use App\Services\Geocoding\GeocodingFailureReason;
use App\Services\Geocoding\GeocodingResult;
use App\Services\GeocodingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class GeocodingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected GeocodingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new GeocodingService();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    #[\PHPUnit\Framework\Attributes\DataProvider('otCityProvider')]
    public function it_extracts_ot_variants_from_city_name(
        string $city,
        string $expectedMain,
        string $expectedDistrict
    ): void {
        $address           = new Address();
        $address->street   = 'Pestalozzistraße';
        $address->number   = '4';
        $address->city     = $city;
        $address->zip_code = '04668';

        $variants = $this->callProtected('buildAddressVariants', $address);

        $cityValues = array_column($variants, 'city');

        $this->assertContains($expectedMain, $cityValues, "Hauptstadt '{$expectedMain}' fehlt in Varianten.");
        $this->assertContains($expectedDistrict, $cityValues, "Ortsteil '{$expectedDistrict}' fehlt in Varianten.");
        // Letzter Eintrag ist immer der Fallback ohne Stadtname
        $this->assertSame('', end($variants)['city'], 'Letzter Fallback muss leeren Stadtnamen haben.');
    }

    public static function otCityProvider(): array
    {
        return [
            'Bindestrich-OT-Format'  => ['Grimma - OT Großbardau',               'Grimma',                    'Großbardau'],
            'Leerzeichen-OT-Format'  => ['Reichenbach im Vogtl. OT Mylau',        'Reichenbach im Vogtl.',      'Mylau'],
            'Einfaches-OT-Format'    => ['Wilsdruff OT Grumbach',                 'Wilsdruff',                  'Grumbach'],
        ];
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_only_returns_original_and_fallback_for_normal_city(): void
    {
        $address           = new Address();
        $address->street   = 'Hauptstraße';
        $address->number   = '1';
        $address->city     = 'Dresden';
        $address->zip_code = '01069';

        $variants = $this->callProtected('buildAddressVariants', $address);

        // Für normale Städte: Original + Nur PLZ+Straße
        $this->assertCount(2, $variants);
        $this->assertSame('Dresden', $variants[0]['city']);
        $this->assertSame('', $variants[1]['city']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_null_when_all_variants_fail(): void
    {
        $address           = new Address();
        $address->street   = 'Nichtexistierendestraße';
        $address->number   = '999';
        $address->city     = 'Nichtexistierendstadt';
        $address->zip_code = '00000';

        // GeocodingService mocken, fetchCoordinates gibt immer null zurück
        $mock = $this->getMockBuilder(GeocodingService::class)
            ->onlyMethods(['fetchCoordinates'])
            ->getMock();

        $mock->method('fetchCoordinates')->willReturn(null);

        $result = $mock->geocode($address);

        $this->assertNull($result);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_first_successful_variant(): void
    {
        $address           = new Address();
        $address->street   = 'Pestalozzistraße';
        $address->number   = '4';
        $address->city     = 'Grimma - OT Großbardau';
        $address->zip_code = '04668';

        $mock = $this->getMockBuilder(GeocodingService::class)
            ->onlyMethods(['fetchCoordinates'])
            ->getMock();

        // Nur die Hauptstadt-Variante liefert ein Ergebnis
        $mock->method('fetchCoordinates')
            ->willReturnCallback(function (string $street, string $city) {
                if ($city === 'Grimma') {
                    return ['lat' => '51.234', 'lon' => '12.722'];
                }

                return null;
            });

        $result = $mock->geocode($address);

        $this->assertNotNull($result);
        $this->assertSame('51.234', $result['lat']);
        $this->assertSame('12.722', $result['lon']);
    }

    private function makeAddress(string $city = 'Dresden', string $zip = '01069'): Address
    {
        $address           = new Address();
        $address->street   = 'Hauptstraße';
        $address->number   = '1';
        $address->city     = $city;
        $address->zip_code = $zip;

        return $address;
    }

    private function configureService(): void
    {
        config(['geocode.geocode_url' => 'https://geocode.test/search?q=', 'geocode.geocode_key' => 'secret-key-123']);
        Http::preventStrayRequests();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_coordinates_from_the_http_response(): void
    {
        $this->configureService();
        Http::fake(['geocode.test/*' => Http::response([['lat' => '51.05', 'lon' => '13.74']])]);

        $result = $this->service->geocodeWithResult($this->makeAddress());

        $this->assertTrue($result->successful());
        $this->assertSame(['lat' => '51.05', 'lon' => '13.74'], $result->coordinates());
        $this->assertNull($result->message());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_reports_no_results_with_all_tried_variants(): void
    {
        $this->configureService();
        Http::fake(['geocode.test/*' => Http::response([])]);

        $result = $this->service->geocodeWithResult($this->makeAddress());

        $this->assertSame(GeocodingFailureReason::NoResults, $result->reason);
        $this->assertNull($this->service->geocode($this->makeAddress()));
        $this->assertStringContainsString('keine Treffer', (string) $result->message());
        $this->assertStringContainsString('Versucht: Original, Nur Straße + PLZ', (string) $result->message());
        Http::assertSentCount(4);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    #[\PHPUnit\Framework\Attributes\DataProvider('httpErrorProvider')]
    public function it_maps_http_errors_to_reasons(int $status, GeocodingFailureReason $expected, int $expectedRequests): void
    {
        $this->configureService();
        Http::fake(['geocode.test/*' => Http::response('error', $status)]);

        $result = $this->service->geocodeWithResult($this->makeAddress());

        $this->assertSame($expected, $result->reason);
        $this->assertStringContainsString("HTTP {$status}", (string) $result->message());
        $this->assertStringNotContainsString('secret-key-123', (string) $result->message());
        Http::assertSentCount($expectedRequests);
    }

    public static function httpErrorProvider(): array
    {
        // Auth- und Rate-Limit-Fehler brechen sofort ab, Serverfehler probieren die übrigen Varianten
        return [
            '401 ungültiger Key' => [401, GeocodingFailureReason::InvalidApiKey, 1],
            '403 gesperrt'       => [403, GeocodingFailureReason::InvalidApiKey, 1],
            '429 Rate-Limit'     => [429, GeocodingFailureReason::RateLimited, 1],
            '500 Serverfehler'   => [500, GeocodingFailureReason::ServiceError, 2],
        ];
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_reports_connection_failures_without_leaking_the_api_key(): void
    {
        $this->configureService();
        Log::spy();
        Http::fake(fn () => throw new ConnectionException('cURL error 28: timeout for https://geocode.test/search?q=x&api_key=secret-key-123'));

        $result = $this->service->geocodeWithResult($this->makeAddress());

        $this->assertSame(GeocodingFailureReason::ConnectionFailed, $result->reason);
        Log::shouldHaveReceived('error')->withArgs(fn (string $message) => ! str_contains($message, 'secret-key-123'));
        Log::shouldNotHaveReceived('debug', fn (string $message) => str_contains($message, 'secret-key-123'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_reports_invalid_json(): void
    {
        $this->configureService();
        Http::fake(['geocode.test/*' => Http::response('<html>kaputt</html>')]);

        $result = $this->service->geocodeWithResult($this->makeAddress());

        $this->assertSame(GeocodingFailureReason::InvalidResponse, $result->reason);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_reports_missing_configuration_without_http_call(): void
    {
        config(['geocode.geocode_url' => 'https://geocode.test/search?q=', 'geocode.geocode_key' => null]);
        Http::fake();

        $result = $this->service->geocodeWithResult($this->makeAddress());

        $this->assertSame(GeocodingFailureReason::NotConfigured, $result->reason);
        Http::assertNothingSent();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_reports_incomplete_address_without_http_call(): void
    {
        $this->configureService();
        Http::fake();

        $result = $this->service->geocodeWithResult($this->makeAddress('', ''));

        $this->assertSame(GeocodingFailureReason::IncompleteAddress, $result->reason);
        Http::assertNothingSent();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function apply_result_sets_and_resets_the_error(): void
    {
        $address = $this->makeAddress();

        $this->service->applyResult($address, GeocodingResult::failure(GeocodingFailureReason::NoResults));
        $this->assertNotNull($address->geocoding_error);
        $this->assertNotNull($address->geocoding_failed_at);

        $this->service->applyResult($address, GeocodingResult::success('51.0', '13.0'));
        $this->assertNull($address->geocoding_error);
        $this->assertNull($address->geocoding_failed_at);
        $this->assertSame('51.0', $address->latitude);
    }

    /**
     * Ruft eine protected-Methode über Reflection auf.
     */
    private function callProtected(string $method, mixed ...$args): mixed
    {
        $reflection = new \ReflectionMethod(GeocodingService::class, $method);
        $reflection->setAccessible(true);

        return $reflection->invoke($this->service, ...$args);
    }
}


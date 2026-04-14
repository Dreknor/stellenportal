<?php

namespace Tests\Unit\Services;

use App\Models\Address;
use App\Services\GeocodingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
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


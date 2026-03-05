<?php
/**
 * Tests for OnOfficeMapper.
 *
 * @package Enteco\ImmoConnector\Tests\Unit
 */

declare(strict_types=1);

namespace Enteco\ImmoConnector\Tests\Unit;

use Brain\Monkey;
use Brain\Monkey\Filters;
use Enteco\ImmoConnector\Api\ApiResponse;
use Enteco\ImmoConnector\Api\OnOffice\OnOfficeMapper;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Enteco\ImmoConnector\Api\OnOffice\OnOfficeMapper
 */
class OnOfficeMapperTest extends TestCase
{
    private OnOfficeMapper $mapper;

    protected function setUp(): void
    {
        parent::setUp();
        Monkey\setUp();
        Filters\expectApplied('eic/mapper/field_value')
            ->zeroOrMoreTimes()
            ->andReturnFirstArg();

        $this->mapper = new OnOfficeMapper();
    }

    protected function tearDown(): void
    {
        Monkey\tearDown();
        parent::tearDown();
    }

    public function test_map_property_returns_api_response(): void
    {
        $record   = ['kaufpreis' => 250000, 'ort' => 'Graz'];
        $response = $this->mapper->map_property($record, 'OO-500');

        self::assertInstanceOf(ApiResponse::class, $response);
    }

    public function test_map_property_sets_source_onoffice(): void
    {
        $response = $this->mapper->map_property([], 'OO-501');
        self::assertSame('onoffice', $response->get_source());
        self::assertSame('OO-501', $response->get_external_id());
    }

    public function test_map_property_maps_kaufpreis(): void
    {
        $record   = ['kaufpreis' => 180000.0];
        $response = $this->mapper->map_property($record, 'OO-502');

        self::assertEqualsWithDelta(180000.0, $response->get_field('eic_kaufpreis'), 0.01);
    }

    public function test_map_property_maps_ort(): void
    {
        $record   = ['ort' => 'Innsbruck', 'plz' => '6020'];
        $response = $this->mapper->map_property($record, 'OO-503');

        self::assertSame('Innsbruck', $response->get_field('eic_ort'));
        self::assertSame('6020', $response->get_field('eic_plz'));
    }

    public function test_map_property_extracts_images_from_record(): void
    {
        $record   = [
            '_images' => ['https://cdn.onoffice.de/img1.jpg', 'https://cdn.onoffice.de/img2.jpg'],
        ];
        $response = $this->mapper->map_property($record, 'OO-504');

        self::assertCount(2, $response->get_images());
    }

    public function test_map_property_sets_api_source_fields(): void
    {
        $response = $this->mapper->map_property([], 'OO-505');

        self::assertSame('onoffice', $response->get_field('eic_api_source'));
        self::assertSame('OO-505', $response->get_field('eic_api_source_id'));
    }

    public function test_map_agent_returns_agent_type(): void
    {
        $record   = ['Vorname' => 'Anna', 'Name' => 'Berger', 'Email' => 'anna@test.at', 'Telefon1' => '+43 664 1234567'];
        $response = $this->mapper->map_agent($record, 'OO-ADDR-10');

        self::assertInstanceOf(ApiResponse::class, $response);
        self::assertSame('agent', $response->get_type());
        self::assertSame('Anna', $response->get_field('eic_vorname'));
        self::assertSame('Berger', $response->get_field('eic_nachname'));
        self::assertSame('anna@test.at', $response->get_field('eic_email'));
    }

    public function test_map_property_casts_wohnflaeche_to_float(): void
    {
        $record   = ['wohnflaeche' => '120'];
        $response = $this->mapper->map_property($record, 'OO-506');

        self::assertIsFloat($response->get_field('eic_wohnflaeche'));
    }

    public function test_empty_record_returns_minimal_response(): void
    {
        $response = $this->mapper->map_property([], 'OO-EMPTY');

        self::assertSame('onoffice', $response->get_source());
        self::assertIsArray($response->get_fields());
    }

    public function test_missing_onoffice_key_does_not_appear_in_fields(): void
    {
        $response = $this->mapper->map_property(['ort' => 'Linz'], 'OO-507');

        // kaufpreis not provided → should not be set.
        self::assertNull($response->get_field('eic_kaufpreis'));
    }
}

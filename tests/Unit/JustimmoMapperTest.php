<?php
/**
 * Tests for JustimmoMapper.
 *
 * @package Enteco\ImmoConnector\Tests\Unit
 */

declare(strict_types=1);

namespace Enteco\ImmoConnector\Tests\Unit;

use Brain\Monkey;
use Brain\Monkey\Filters;
use Enteco\ImmoConnector\Api\ApiResponse;
use Enteco\ImmoConnector\Api\Justimmo\JustimmoMapper;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Enteco\ImmoConnector\Api\Justimmo\JustimmoMapper
 */
class JustimmoMapperTest extends TestCase
{
    private JustimmoMapper $mapper;

    protected function setUp(): void
    {
        parent::setUp();
        Monkey\setUp();
        // Allow apply_filters to pass values through.
        Filters\expectApplied('eic/mapper/field_value')
            ->zeroOrMoreTimes()
            ->andReturnFirstArg();

        $this->mapper = new JustimmoMapper();
    }

    protected function tearDown(): void
    {
        Monkey\tearDown();
        parent::tearDown();
    }

    private function make_xml(array $fields, array $images = []): \SimpleXMLElement
    {
        $xml_str = '<immobilie>';

        // Build nested XML from flat field map.
        foreach ($fields as $key => $value) {
            $xml_str .= "<{$key}>" . htmlspecialchars((string) $value, ENT_XML1) . "</{$key}>";
        }

        if (!empty($images)) {
            $xml_str .= '<anhaenge>';
            foreach ($images as $url) {
                $xml_str .= '<anhang><daten><pfad>' . htmlspecialchars($url, ENT_XML1) . '</pfad></daten></anhang>';
            }
            $xml_str .= '</anhaenge>';
        }

        $xml_str .= '</immobilie>';

        $xml = simplexml_load_string($xml_str);
        self::assertInstanceOf(\SimpleXMLElement::class, $xml);
        return $xml;
    }

    public function test_map_property_returns_api_response(): void
    {
        $xml      = $this->make_xml(['kaufpreis' => '350000', 'ort' => 'Wien']);
        $response = $this->mapper->map_property($xml, 'JM-100');

        self::assertInstanceOf(ApiResponse::class, $response);
    }

    public function test_map_property_sets_source_and_id(): void
    {
        $xml      = $this->make_xml([]);
        $response = $this->mapper->map_property($xml, 'JM-100');

        self::assertSame('justimmo', $response->get_source());
        self::assertSame('JM-100', $response->get_external_id());
    }

    public function test_map_property_maps_kaufpreis(): void
    {
        $xml      = $this->make_xml(['kaufpreis' => '350000.50']);
        $response = $this->mapper->map_property($xml, 'JM-101');

        self::assertEqualsWithDelta(350000.50, $response->get_field('eic_kaufpreis'), 0.01);
    }

    public function test_map_property_maps_ort_and_plz(): void
    {
        $xml      = $this->make_xml(['ort' => 'Salzburg', 'plz' => '5020']);
        $response = $this->mapper->map_property($xml, 'JM-102');

        self::assertSame('Salzburg', $response->get_field('eic_ort'));
        self::assertSame('5020', $response->get_field('eic_plz'));
    }

    public function test_map_property_maps_wohnflaeche_as_float(): void
    {
        $xml      = $this->make_xml(['wohnflaeche' => '85,5']);
        $response = $this->mapper->map_property($xml, 'JM-103');

        self::assertEqualsWithDelta(85.5, $response->get_field('eic_wohnflaeche'), 0.01);
    }

    public function test_map_property_extracts_images(): void
    {
        $xml = $this->make_xml(
            [],
            ['https://cdn.justimmo.at/img1.jpg', 'https://cdn.justimmo.at/img2.jpg']
        );
        $response = $this->mapper->map_property($xml, 'JM-104');

        self::assertCount(2, $response->get_images());
        self::assertSame('https://cdn.justimmo.at/img1.jpg', $response->get_cover_image());
    }

    public function test_map_property_sets_api_source_fields(): void
    {
        $xml      = $this->make_xml([]);
        $response = $this->mapper->map_property($xml, 'JM-105');

        self::assertSame('justimmo', $response->get_field('eic_api_source'));
        self::assertSame('JM-105', $response->get_field('eic_api_source_id'));
    }

    public function test_map_agent_returns_api_response_with_agent_type(): void
    {
        $xml_str = '<mitarbeiter><vorname>Max</vorname><nachname>Mustermann</nachname><email>max@example.com</email><tel_zentrale>+43 1 234567</tel_zentrale></mitarbeiter>';
        $xml     = simplexml_load_string($xml_str);

        $response = $this->mapper->map_agent($xml, 'JM-AGENT-1');

        self::assertInstanceOf(ApiResponse::class, $response);
        self::assertSame('agent', $response->get_type());
        self::assertSame('Max', $response->get_field('eic_vorname'));
        self::assertSame('Mustermann', $response->get_field('eic_nachname'));
        self::assertSame('max@example.com', $response->get_field('eic_email'));
    }

    public function test_cast_bool_via_mapper_with_x_value(): void
    {
        $xml      = $this->make_xml(['barrierefrei' => 'x']);
        $response = $this->mapper->map_property($xml, 'JM-106');

        self::assertTrue((bool) $response->get_field('eic_barrierefrei'));
    }

    public function test_empty_xml_produces_minimal_response(): void
    {
        $xml      = $this->make_xml([]);
        $response = $this->mapper->map_property($xml, 'JM-EMPTY');

        self::assertSame('justimmo', $response->get_source());
        self::assertIsArray($response->get_fields());
    }
}

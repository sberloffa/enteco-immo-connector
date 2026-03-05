<?php
/**
 * Tests for ApiResponse value object.
 *
 * @package Enteco\ImmoConnector\Tests\Unit
 */

declare(strict_types=1);

namespace Enteco\ImmoConnector\Tests\Unit;

use Enteco\ImmoConnector\Api\ApiResponse;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Enteco\ImmoConnector\Api\ApiResponse
 */
class ApiResponseTest extends TestCase
{
    private ApiResponse $response;

    protected function setUp(): void
    {
        $this->response = new ApiResponse(
            source: 'justimmo',
            external_id: 'JM-123',
            fields: [
                'eic_kaufpreis' => 350000.0,
                'eic_wohnflaeche' => 85.5,
                'eic_ort' => 'Wien',
                'eic_plz' => '1010',
            ],
            images: [
                'https://example.com/img1.jpg',
                'https://example.com/img2.jpg',
            ],
            documents: [],
            type: 'property',
        );
    }

    public function test_get_source(): void
    {
        self::assertSame('justimmo', $this->response->get_source());
    }

    public function test_get_external_id(): void
    {
        self::assertSame('JM-123', $this->response->get_external_id());
    }

    public function test_get_fields_returns_array(): void
    {
        $fields = $this->response->get_fields();
        self::assertIsArray($fields);
        self::assertArrayHasKey('eic_kaufpreis', $fields);
        self::assertSame(350000.0, $fields['eic_kaufpreis']);
    }

    public function test_get_field_returns_single_value(): void
    {
        self::assertSame('Wien', $this->response->get_field('eic_ort'));
    }

    public function test_get_field_returns_null_for_missing_key(): void
    {
        self::assertNull($this->response->get_field('eic_nonexistent'));
    }

    public function test_get_images_returns_array(): void
    {
        $images = $this->response->get_images();
        self::assertCount(2, $images);
    }

    public function test_get_cover_image_returns_first(): void
    {
        self::assertSame('https://example.com/img1.jpg', $this->response->get_cover_image());
    }

    public function test_get_cover_image_empty_when_no_images(): void
    {
        $empty = new ApiResponse('justimmo', 'JM-999', [], [], []);
        self::assertSame('', $empty->get_cover_image());
    }

    public function test_get_type_returns_property(): void
    {
        self::assertSame('property', $this->response->get_type());
    }

    public function test_build_hash_is_string(): void
    {
        $hash = $this->response->build_hash();
        self::assertIsString($hash);
        self::assertNotEmpty($hash);
    }

    public function test_build_hash_is_deterministic(): void
    {
        self::assertSame(
            $this->response->build_hash(),
            $this->response->build_hash()
        );
    }

    public function test_build_hash_differs_for_different_data(): void
    {
        $other = new ApiResponse('justimmo', 'JM-123', ['eic_kaufpreis' => 400000.0]);
        self::assertNotSame($this->response->build_hash(), $other->build_hash());
    }

    public function test_with_fields_returns_new_instance(): void
    {
        $updated = $this->response->with_fields(['eic_strasse' => 'Hauptstraße 1']);
        self::assertNotSame($this->response, $updated);
        self::assertSame('Hauptstraße 1', $updated->get_field('eic_strasse'));
        // Original unchanged.
        self::assertNull($this->response->get_field('eic_strasse'));
    }

    public function test_with_fields_overrides_existing(): void
    {
        $updated = $this->response->with_fields(['eic_kaufpreis' => 500000.0]);
        self::assertSame(500000.0, $updated->get_field('eic_kaufpreis'));
    }

    public function test_agent_type(): void
    {
        $agent = new ApiResponse('onoffice', 'OO-42', [], [], [], 'agent');
        self::assertSame('agent', $agent->get_type());
    }
}

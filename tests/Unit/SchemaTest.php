<?php
/**
 * Tests for OpenImmo Schema.
 *
 * @package Enteco\ImmoConnector\Tests\Unit
 */

declare(strict_types=1);

namespace Enteco\ImmoConnector\Tests\Unit;

use Enteco\ImmoConnector\OpenImmo\Schema;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Enteco\ImmoConnector\OpenImmo\Schema
 */
class SchemaTest extends TestCase
{
    public function test_get_field_map_returns_non_empty_array(): void
    {
        $map = Schema::get_field_map();
        self::assertIsArray($map);
        self::assertNotEmpty($map);
    }

    public function test_all_mapped_values_have_eic_prefix(): void
    {
        foreach (Schema::get_field_map() as $oi_key => $eic_key) {
            self::assertStringStartsWith(
                'eic_',
                $eic_key,
                "Schema value does not have eic_ prefix: $oi_key => $eic_key"
            );
        }
    }

    public function test_essential_keys_mapped(): void
    {
        $map = Schema::get_field_map();
        $essential = [
            'kaufpreis'    => 'eic_kaufpreis',
            'kaltmiete'    => 'eic_kaltmiete',
            'wohnflaeche'  => 'eic_wohnflaeche',
            'plz'          => 'eic_plz',
            'ort'          => 'eic_ort',
        ];
        foreach ($essential as $oi => $eic) {
            self::assertArrayHasKey($oi, $map);
            self::assertSame($eic, $map[$oi]);
        }
    }

    public function test_get_reverse_map_inverts_field_map(): void
    {
        $map     = Schema::get_field_map();
        $reverse = Schema::get_reverse_map();

        foreach ($map as $oi => $eic) {
            self::assertArrayHasKey($eic, $reverse);
            self::assertSame($oi, $reverse[$eic]);
        }
    }

    public function test_no_duplicate_eic_keys_in_map(): void
    {
        $values = array_values(Schema::get_field_map());
        self::assertSame(
            count($values),
            count(array_unique($values)),
            'Duplicate eic_ keys found in Schema field map'
        );
    }
}

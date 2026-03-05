<?php
/**
 * Tests for FieldDefinitions – Single Source of Truth.
 *
 * @package Enteco\ImmoConnector\Tests\Unit
 */

declare(strict_types=1);

namespace Enteco\ImmoConnector\Tests\Unit;

use Enteco\ImmoConnector\PostTypes\FieldEngine\FieldDefinitions;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Enteco\ImmoConnector\PostTypes\FieldEngine\FieldDefinitions
 */
class FieldDefinitionsTest extends TestCase
{
    public function test_get_property_fields_returns_non_empty_array(): void
    {
        $fields = FieldDefinitions::get_property_fields();
        self::assertIsArray($fields);
        self::assertNotEmpty($fields);
    }

    public function test_every_field_has_required_keys(): void
    {
        foreach (FieldDefinitions::get_property_fields() as $field) {
            self::assertArrayHasKey('key', $field, "Field missing 'key': " . json_encode($field));
            self::assertArrayHasKey('type', $field, "Field missing 'type': " . $field['key']);
            self::assertArrayHasKey('label', $field, "Field missing 'label': " . $field['key']);
            self::assertArrayHasKey('group', $field, "Field missing 'group': " . $field['key']);
        }
    }

    public function test_all_field_keys_have_eic_prefix(): void
    {
        foreach (FieldDefinitions::get_property_fields() as $field) {
            self::assertStringStartsWith(
                'eic_',
                $field['key'],
                "Field key does not start with eic_: " . $field['key']
            );
        }
    }

    public function test_field_types_are_valid(): void
    {
        $valid_types = [
            'string', 'float', 'int', 'bool', 'select',
            'multiselect', 'email', 'url', 'date', 'datetime', 'textarea',
        ];

        foreach (FieldDefinitions::get_property_fields() as $field) {
            self::assertContains(
                $field['type'],
                $valid_types,
                "Invalid type '{$field['type']}' for field '{$field['key']}'"
            );
        }
    }

    public function test_field_keys_are_unique(): void
    {
        $keys = FieldDefinitions::get_field_keys();
        self::assertSame(count($keys), count(array_unique($keys)), 'Duplicate field keys detected');
    }

    public function test_groups_are_valid(): void
    {
        $valid_groups = [
            'geo', 'preise', 'flaechen', 'ausstattung',
            'freitexte', 'verwaltung_techn', 'verwaltung_objekt',
        ];

        foreach (FieldDefinitions::get_property_fields() as $field) {
            self::assertContains(
                $field['group'],
                $valid_groups,
                "Invalid group '{$field['group']}' for field '{$field['key']}'"
            );
        }
    }

    public function test_get_field_returns_field_by_key(): void
    {
        $field = FieldDefinitions::get_field('eic_kaufpreis');
        self::assertNotNull($field);
        self::assertSame('eic_kaufpreis', $field['key']);
        self::assertSame('float', $field['type']);
    }

    public function test_get_field_returns_null_for_unknown_key(): void
    {
        self::assertNull(FieldDefinitions::get_field('eic_does_not_exist'));
    }

    public function test_get_field_keys_returns_strings(): void
    {
        $keys = FieldDefinitions::get_field_keys();
        self::assertIsArray($keys);
        foreach ($keys as $key) {
            self::assertIsString($key);
        }
    }

    public function test_get_fields_by_group_geo(): void
    {
        $geo = FieldDefinitions::get_fields_by_group('geo');
        self::assertNotEmpty($geo);
        foreach ($geo as $field) {
            self::assertSame('geo', $field['group']);
        }
    }

    public function test_get_fields_by_group_preise(): void
    {
        $preise = FieldDefinitions::get_fields_by_group('preise');
        self::assertNotEmpty($preise);
        $keys = array_column($preise, 'key');
        self::assertContains('eic_kaufpreis', $keys);
        self::assertContains('eic_kaltmiete', $keys);
    }

    public function test_get_fields_by_group_flaechen(): void
    {
        $flaechen = FieldDefinitions::get_fields_by_group('flaechen');
        self::assertNotEmpty($flaechen);
        $keys = array_column($flaechen, 'key');
        self::assertContains('eic_wohnflaeche', $keys);
        self::assertContains('eic_anzahl_zimmer', $keys);
    }

    public function test_get_fields_by_group_returns_empty_for_unknown(): void
    {
        $result = FieldDefinitions::get_fields_by_group('nonexistent_group');
        self::assertIsArray($result);
        self::assertEmpty($result);
    }

    public function test_agent_fields_have_eic_prefix(): void
    {
        foreach (FieldDefinitions::get_agent_fields() as $field) {
            self::assertStringStartsWith('eic_', $field['key']);
        }
    }

    public function test_essential_property_fields_exist(): void
    {
        $keys = FieldDefinitions::get_field_keys();
        $essential = [
            'eic_kaufpreis', 'eic_kaltmiete', 'eic_wohnflaeche',
            'eic_plz', 'eic_ort', 'eic_anzahl_zimmer',
            'eic_api_source', 'eic_api_source_id',
        ];
        foreach ($essential as $key) {
            self::assertContains($key, $keys, "Essential field missing: $key");
        }
    }

    public function test_ausstattung_has_exactly_10_fields(): void
    {
        $ausstattung = FieldDefinitions::get_fields_by_group('ausstattung');
        // FREE tier should have exactly 10 ausstattung fields.
        self::assertCount(10, $ausstattung);
    }
}

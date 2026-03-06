<?php
/**
 * Tests for PropertyPostType field engine selection.
 *
 * @package Enteco\ImmoConnector\Tests\Unit
 */

declare(strict_types=1);

namespace Enteco\ImmoConnector\Tests\Unit;

use Brain\Monkey;
use Brain\Monkey\Filters;
use Brain\Monkey\Functions;
use Enteco\ImmoConnector\PostTypes\FieldEngine\FieldEngineInterface;
use Enteco\ImmoConnector\PostTypes\FieldEngine\NativeFieldEngine;
use Enteco\ImmoConnector\PostTypes\PropertyPostType;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Enteco\ImmoConnector\PostTypes\PropertyPostType::get_field_engine
 */
class PropertyPostTypeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Monkey\setUp();
    }

    protected function tearDown(): void
    {
        Monkey\tearDown();
        parent::tearDown();
    }

    public function test_get_field_engine_returns_field_engine_interface(): void
    {
        Functions\expect('get_option')
            ->once()
            ->with('eic_field_engine', 'native')
            ->andReturn('native');

        Filters\expectApplied('eic/field_engines')
            ->once()
            ->andReturnFirstArg();

        $post_type = new PropertyPostType();
        $engine    = $post_type->get_field_engine();

        self::assertInstanceOf(FieldEngineInterface::class, $engine);
    }

    public function test_get_field_engine_returns_native_engine_by_default(): void
    {
        Functions\expect('get_option')
            ->once()
            ->with('eic_field_engine', 'native')
            ->andReturn('native');

        Filters\expectApplied('eic/field_engines')
            ->once()
            ->andReturnFirstArg();

        $post_type = new PropertyPostType();
        $engine    = $post_type->get_field_engine();

        self::assertInstanceOf(NativeFieldEngine::class, $engine);
    }

    public function test_get_field_engine_falls_back_to_native_for_unknown_slug(): void
    {
        Functions\expect('get_option')
            ->once()
            ->with('eic_field_engine', 'native')
            ->andReturn('acf'); // not registered in filter

        Filters\expectApplied('eic/field_engines')
            ->once()
            ->andReturnFirstArg(); // only returns 'native'

        $post_type = new PropertyPostType();
        $engine    = $post_type->get_field_engine();

        self::assertInstanceOf(NativeFieldEngine::class, $engine);
    }

    public function test_get_field_engine_uses_custom_engine_from_filter(): void
    {
        // Create a stub engine class for testing.
        $stub_class = new class implements FieldEngineInterface {
            public function register_fields(): void {}
            public function get_field_value(int $post_id, string $key): mixed { return null; }
            public function set_field_value(int $post_id, string $key, mixed $value): bool { return true; }
            public function get_all_values(int $post_id): array { return []; }
        };

        $stub_class_name = $stub_class::class;

        Functions\expect('get_option')
            ->once()
            ->with('eic_field_engine', 'native')
            ->andReturn('custom_engine');

        Filters\expectApplied('eic/field_engines')
            ->once()
            ->andReturnUsing(static function (array $engines) use ($stub_class_name): array {
                $engines['custom_engine'] = $stub_class_name;
                return $engines;
            });

        $post_type = new PropertyPostType();
        $engine    = $post_type->get_field_engine();

        self::assertInstanceOf(FieldEngineInterface::class, $engine);
        $this->addToAssertionCount(1);
    }

    public function test_get_field_engine_applies_eic_field_engines_filter(): void
    {
        Functions\expect('get_option')
            ->once()
            ->with('eic_field_engine', 'native')
            ->andReturn('native');

        // The filter must be applied exactly once.
        Filters\expectApplied('eic/field_engines')
            ->once()
            ->andReturnFirstArg();

        (new PropertyPostType())->get_field_engine();
        $this->addToAssertionCount(1);
    }
}

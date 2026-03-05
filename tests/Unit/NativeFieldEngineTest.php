<?php
/**
 * Tests for NativeFieldEngine.
 *
 * @package Enteco\ImmoConnector\Tests\Unit
 */

declare(strict_types=1);

namespace Enteco\ImmoConnector\Tests\Unit;

use Brain\Monkey;
use Brain\Monkey\Functions;
use Enteco\ImmoConnector\PostTypes\FieldEngine\NativeFieldEngine;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Enteco\ImmoConnector\PostTypes\FieldEngine\NativeFieldEngine
 */
class NativeFieldEngineTest extends TestCase
{
    private NativeFieldEngine $engine;

    protected function setUp(): void
    {
        parent::setUp();
        Monkey\setUp();
        $this->engine = new NativeFieldEngine();
    }

    protected function tearDown(): void
    {
        Monkey\tearDown();
        parent::tearDown();
    }

    public function test_implements_field_engine_interface(): void
    {
        self::assertInstanceOf(
            \Enteco\ImmoConnector\PostTypes\FieldEngine\FieldEngineInterface::class,
            $this->engine
        );
    }

    public function test_get_field_value_calls_get_post_meta(): void
    {
        Functions\expect('get_post_meta')
            ->once()
            ->with(42, 'eic_kaufpreis', true)
            ->andReturn(350000.0);

        $value = $this->engine->get_field_value(42, 'eic_kaufpreis');
        self::assertSame(350000.0, $value);
    }

    public function test_set_field_value_calls_update_post_meta(): void
    {
        Functions\expect('update_post_meta')
            ->once()
            ->with(42, 'eic_kaufpreis', 350000.0)
            ->andReturn(true);

        $result = $this->engine->set_field_value(42, 'eic_kaufpreis', 350000.0);
        self::assertTrue($result);
    }

    public function test_set_field_value_returns_false_on_failure(): void
    {
        Functions\expect('update_post_meta')
            ->once()
            ->andReturn(false);

        $result = $this->engine->set_field_value(99, 'eic_kaufpreis', 0.0);
        self::assertFalse($result);
    }

    public function test_get_all_values_returns_array_for_all_keys(): void
    {
        Functions\expect('get_post_meta')
            ->zeroOrMoreTimes()
            ->andReturn('');

        $values = $this->engine->get_all_values(1);
        self::assertIsArray($values);
        self::assertNotEmpty($values);

        // All keys should have eic_ prefix.
        foreach (array_keys($values) as $key) {
            self::assertStringStartsWith('eic_', $key);
        }
    }

    public function test_register_fields_adds_init_action(): void
    {
        Functions\expect('add_action')
            ->once()
            ->with('init', [$this->engine, 'do_register_fields']);

        $this->engine->register_fields();
        $this->addToAssertionCount(1); // Brain\Monkey verifies the add_action call.
    }
}

<?php
/**
 * Tests for extension hooks: eic_object_limit and SettingsPage engine guard.
 *
 * @package Enteco\ImmoConnector\Tests\Unit
 */

declare(strict_types=1);

namespace Enteco\ImmoConnector\Tests\Unit;

use Brain\Monkey;
use Brain\Monkey\Filters;
use Brain\Monkey\Functions;
use Enteco\ImmoConnector\Api\ApiResponse;
use Enteco\ImmoConnector\Import\ImportDiff;
use Enteco\ImmoConnector\Import\MediaHandler;
use Enteco\ImmoConnector\Import\PropertyImporter;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Enteco\ImmoConnector\Import\PropertyImporter
 * @covers \Enteco\ImmoConnector\Admin\SettingsPage
 */
class ImportEngineExtensionTest extends TestCase
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

    // ──────────────────────────────────────────────────────────────────────────
    // eic_object_limit filter tests
    // ──────────────────────────────────────────────────────────────────────────

    public function test_object_limit_filter_is_applied_on_create(): void
    {
        $count          = new \stdClass();
        $count->publish = 50;
        $count->draft   = 0;

        Functions\when('do_action')->justReturn();

        Functions\expect('wp_count_posts')
            ->once()
            ->with('eic_property')
            ->andReturn($count);

        Filters\expectApplied('eic_object_limit')
            ->once()
            ->with(50)
            ->andReturnFirstArg();

        Functions\expect('__')
            ->zeroOrMoreTimes()
            ->andReturnFirstArg();

        Functions\when('get_posts')->justReturn([]);

        $response = $this->make_response('TEST-1');
        $diff     = new ImportDiff('test', []);
        $importer = new PropertyImporter(new MediaHandler(), $diff);

        $this->expectException(\RuntimeException::class);
        $importer->import($response);
    }

    public function test_pro_limit_override_allows_import_past_50(): void
    {
        $count          = new \stdClass();
        $count->publish = 50;
        $count->draft   = 0;

        Functions\expect('wp_count_posts')
            ->once()
            ->with('eic_property')
            ->andReturn($count);

        Filters\expectApplied('eic_object_limit')
            ->once()
            ->with(50)
            ->andReturn(PHP_INT_MAX);

        Functions\expect('__')->zeroOrMoreTimes()->andReturnFirstArg();
        Functions\expect('wp_insert_post')->once()->andReturn(42);
        Functions\expect('is_wp_error')->once()->with(42)->andReturn(false);
        Functions\expect('update_post_meta')->zeroOrMoreTimes()->andReturn(true);
        Functions\expect('register_meta')->zeroOrMoreTimes();
        Functions\expect('wp_set_object_terms')->zeroOrMoreTimes();
        Functions\when('do_action')->justReturn();
        Functions\when('get_posts')->justReturn([]);

        $response = $this->make_response('TEST-2');
        $diff     = new ImportDiff('test', []);
        $importer = new PropertyImporter(new MediaHandler(), $diff);
        $post_id  = $importer->import($response);

        self::assertSame(42, $post_id);
    }

    public function test_update_path_does_not_check_free_limit(): void
    {
        Functions\expect('wp_count_posts')->never();

        // Make WP_Query return post ID 7 so ImportDiff::find_post_id() returns 7 → UPDATE path.
        \WP_Query::$stub_posts = [7];

        Functions\expect('wp_update_post')->once()->andReturn(7);
        Functions\expect('update_post_meta')->zeroOrMoreTimes()->andReturn(true);
        Functions\expect('register_meta')->zeroOrMoreTimes();
        Functions\expect('wp_set_object_terms')->zeroOrMoreTimes();
        Functions\when('do_action')->justReturn();
        Functions\expect('__')->zeroOrMoreTimes()->andReturnFirstArg();

        $response = $this->make_response('TEST-3');
        $diff     = new ImportDiff('test', []);
        $importer = new PropertyImporter(new MediaHandler(), $diff);
        $post_id  = $importer->import($response);

        self::assertSame(7, $post_id);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // SettingsPage engine guard
    // ──────────────────────────────────────────────────────────────────────────

    public function test_settings_page_engine_guard_locks_after_onboarding(): void
    {
        // Use a single when() callback to handle both get_option calls.
        Functions\when('get_option')->alias(static function (string $key, mixed $default = false): mixed {
            return match ($key) {
                'eic_onboarding_complete' => true,
                'eic_field_engine'        => 'acf',
                default                   => $default,
            };
        });

        $settings = new \Enteco\ImmoConnector\Admin\SettingsPage();
        $result   = $settings->sanitize_field_engine('metabox');

        self::assertSame('acf', $result);
    }

    public function test_settings_page_engine_guard_allows_valid_engine_before_onboarding(): void
    {
        Functions\expect('get_option')
            ->with('eic_onboarding_complete')
            ->andReturn(false);

        Functions\when('sanitize_key')->returnArg();

        Filters\expectApplied('eic/field_engines')
            ->once()
            ->andReturnUsing(static function (array $engines): array {
                $engines['acf'] = 'SomeAcfClass';
                return $engines;
            });

        $settings = new \Enteco\ImmoConnector\Admin\SettingsPage();
        $result   = $settings->sanitize_field_engine('acf');

        self::assertSame('acf', $result);
    }

    public function test_settings_page_engine_guard_rejects_unknown_engine(): void
    {
        Functions\expect('get_option')
            ->with('eic_onboarding_complete')
            ->andReturn(false);

        Functions\when('sanitize_key')->returnArg();

        Filters\expectApplied('eic/field_engines')
            ->once()
            ->andReturnFirstArg();

        $settings = new \Enteco\ImmoConnector\Admin\SettingsPage();
        $result   = $settings->sanitize_field_engine('unknown_engine');

        self::assertSame('native', $result);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────────────────────

    private function make_response(string $external_id): ApiResponse
    {
        return new ApiResponse(
            source:      'test',
            external_id: $external_id,
            fields:      ['eic_objekttitel' => 'Test Objekt'],
            images:      [],
            documents:   [],
            type:        'property'
        );
    }
}

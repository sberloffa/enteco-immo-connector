<?php
/**
 * Tests for ImportDiff.
 *
 * @package Enteco\ImmoConnector\Tests\Unit
 */

declare(strict_types=1);

namespace Enteco\ImmoConnector\Tests\Unit;

use Brain\Monkey;
use Brain\Monkey\Functions;
use Enteco\ImmoConnector\Import\ImportDiff;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Enteco\ImmoConnector\Import\ImportDiff
 */
class ImportDiffTest extends TestCase
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

    private function make_diff(array $remote_ids): ImportDiff
    {
        global $wpdb;

        // Mock $wpdb for get_existing_ids.
        $wpdb = $this->createMock(\stdClass::class);
        $wpdb->postmeta = 'wp_postmeta';
        $wpdb->method('__get')->willReturn('wp_postmeta');
        $wpdb->method('get_col')->willReturn([]);
        $wpdb->method('prepare')->willReturn('SQL');

        return new ImportDiff('justimmo', $remote_ids);
    }

    public function test_get_new_ids_returns_ids_not_in_existing(): void
    {
        global $wpdb;
        $wpdb         = new \stdClass();
        $wpdb->postmeta = 'wp_postmeta';

        // Patch get_existing_ids by mocking wpdb.
        $mock_wpdb          = $this->getMockBuilder(\stdClass::class)
            ->addMethods(['prepare', 'get_col'])
            ->getMock();
        $mock_wpdb->postmeta = 'wp_postmeta';
        $mock_wpdb->method('prepare')->willReturn('PREPARED_SQL');
        $mock_wpdb->method('get_col')->willReturn(['JM-1', 'JM-2']);

        $GLOBALS['wpdb'] = $mock_wpdb;

        $diff    = new ImportDiff('justimmo', ['JM-1', 'JM-2', 'JM-3', 'JM-4']);
        $new_ids = $diff->get_new_ids();

        self::assertContains('JM-3', $new_ids);
        self::assertContains('JM-4', $new_ids);
        self::assertNotContains('JM-1', $new_ids);
    }

    public function test_get_removed_ids_returns_ids_not_in_remote(): void
    {
        $mock_wpdb          = $this->getMockBuilder(\stdClass::class)
            ->addMethods(['prepare', 'get_col'])
            ->getMock();
        $mock_wpdb->postmeta = 'wp_postmeta';
        $mock_wpdb->method('prepare')->willReturn('PREPARED_SQL');
        $mock_wpdb->method('get_col')->willReturn(['JM-1', 'JM-2', 'JM-OLD']);

        $GLOBALS['wpdb'] = $mock_wpdb;

        $diff        = new ImportDiff('justimmo', ['JM-1', 'JM-2']);
        $removed_ids = $diff->get_removed_ids();

        self::assertContains('JM-OLD', $removed_ids);
        self::assertNotContains('JM-1', $removed_ids);
    }

    public function test_needs_update_returns_true_when_hash_differs(): void
    {
        Functions\expect('get_post_meta')
            ->once()
            ->with(10, 'eic_import_hash', true)
            ->andReturn('old_hash_abc');

        $mock_wpdb          = $this->getMockBuilder(\stdClass::class)
            ->addMethods(['prepare', 'get_col'])
            ->getMock();
        $mock_wpdb->postmeta = 'wp_postmeta';
        $mock_wpdb->method('get_col')->willReturn([]);
        $mock_wpdb->method('prepare')->willReturn('SQL');
        $GLOBALS['wpdb'] = $mock_wpdb;

        $diff = new ImportDiff('justimmo', []);
        self::assertTrue($diff->needs_update(10, 'new_hash_xyz'));
    }

    public function test_needs_update_returns_false_when_hash_matches(): void
    {
        Functions\expect('get_post_meta')
            ->once()
            ->with(10, 'eic_import_hash', true)
            ->andReturn('same_hash_123');

        $mock_wpdb          = $this->getMockBuilder(\stdClass::class)
            ->addMethods(['prepare', 'get_col'])
            ->getMock();
        $mock_wpdb->postmeta = 'wp_postmeta';
        $mock_wpdb->method('get_col')->willReturn([]);
        $mock_wpdb->method('prepare')->willReturn('SQL');
        $GLOBALS['wpdb'] = $mock_wpdb;

        $diff = new ImportDiff('justimmo', []);
        self::assertFalse($diff->needs_update(10, 'same_hash_123'));
    }
}

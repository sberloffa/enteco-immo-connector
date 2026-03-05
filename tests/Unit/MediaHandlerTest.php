<?php
/**
 * Tests for MediaHandler.
 *
 * @package Enteco\ImmoConnector\Tests\Unit
 */

declare(strict_types=1);

namespace Enteco\ImmoConnector\Tests\Unit;

use Brain\Monkey;
use Brain\Monkey\Functions;
use Enteco\ImmoConnector\Import\MediaHandler;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Enteco\ImmoConnector\Import\MediaHandler
 */
class MediaHandlerTest extends TestCase
{
    private MediaHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();
        Monkey\setUp();
        $this->handler = new MediaHandler();
    }

    protected function tearDown(): void
    {
        Monkey\tearDown();
        parent::tearDown();
    }

    public function test_set_gallery_urls_stores_meta(): void
    {
        Functions\expect('update_post_meta')
            ->once()
            ->with(5, 'eic_gallery_urls', \Mockery::type('array'));

        Functions\expect('esc_url_raw')
            ->twice()
            ->andReturnFirstArg();

        $this->handler->set_gallery_urls(5, ['https://a.com/1.jpg', 'https://a.com/2.jpg']);
        $this->addToAssertionCount(1); // Brain\Monkey verifies the call above.
    }

    public function test_set_gallery_urls_empty_deletes_meta(): void
    {
        Functions\expect('delete_post_meta')
            ->once()
            ->with(5, 'eic_gallery_urls');

        $this->handler->set_gallery_urls(5, []);
        $this->addToAssertionCount(1);
    }

    public function test_get_gallery_urls_returns_array(): void
    {
        Functions\expect('get_post_meta')
            ->once()
            ->with(5, 'eic_gallery_urls', true)
            ->andReturn(['https://a.com/1.jpg']);

        $urls = $this->handler->get_gallery_urls(5);
        self::assertIsArray($urls);
        self::assertCount(1, $urls);
    }

    public function test_get_gallery_urls_returns_empty_array_on_non_array(): void
    {
        Functions\expect('get_post_meta')
            ->once()
            ->with(5, 'eic_gallery_urls', true)
            ->andReturn('');

        $urls = $this->handler->get_gallery_urls(5);
        self::assertSame([], $urls);
    }

    public function test_set_cover_image_returns_null_for_empty_url(): void
    {
        $result = $this->handler->set_cover_image(1, '');
        self::assertNull($result);
    }

    public function test_set_cover_image_uses_cached_attachment_id(): void
    {
        $url         = 'https://example.com/cover.jpg';
        $fingerprint = 'eic_img_' . md5($url);

        Functions\expect('get_transient')
            ->once()
            ->with($fingerprint)
            ->andReturn(99);

        Functions\expect('get_post')
            ->once()
            ->with(99)
            ->andReturn((object) ['ID' => 99]);

        Functions\expect('set_post_thumbnail')
            ->once()
            ->with(1, 99);

        $result = $this->handler->set_cover_image(1, $url);
        self::assertSame(99, $result);
    }
}

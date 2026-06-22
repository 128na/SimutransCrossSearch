<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Logging;

use App\Actions\Logging\SecretScrubber;
use Illuminate\Support\Facades\Config;
use Tests\Feature\TestCase;

/**
 * C3 / B4: ログ・Discord 送出経路に既知の機密値が混入しないこと。
 */
final class SecretScrubberTest extends TestCase
{
    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        Config::set('services.notion.secret', 'ntn_supersecret');
        Config::set('logging.channels.discord.url', 'https://discord.com/api/webhooks/abc/xyz');
        Config::set('database.connections.mysql.password', 'db-pass-123');
    }

    public function test_scrub_masks_known_secrets_in_strings(): void
    {
        $secretScrubber = new SecretScrubber;

        $result = $secretScrubber->scrub('Authorization: Bearer ntn_supersecret failed for db-pass-123');

        $this->assertStringNotContainsString('ntn_supersecret', $result);
        $this->assertStringNotContainsString('db-pass-123', $result);
        $this->assertStringContainsString('[REDACTED]', $result);
    }

    public function test_scrub_array_recurses_and_masks_throwable(): void
    {
        $secretScrubber = new SecretScrubber;

        $result = $secretScrubber->scrubArray([
            'url' => 'https://discord.com/api/webhooks/abc/xyz',
            'nested' => ['token' => 'value=ntn_supersecret'],
            'exception' => new \RuntimeException('leaked ntn_supersecret here'),
        ]);

        $encoded = json_encode($result);
        $this->assertIsString($encoded);
        $this->assertStringNotContainsString('ntn_supersecret', $encoded);
        $this->assertStringNotContainsString('discord.com/api/webhooks/abc/xyz', $encoded);
    }

    public function test_scrub_is_noop_when_no_secrets_configured(): void
    {
        Config::set('services.notion.secret', '');
        Config::set('logging.channels.discord.url', '');
        Config::set('database.connections.mysql.password', '');
        Config::set('database.connections.portal.password', '');

        $secretScrubber = new SecretScrubber;

        // 空の機密値で全文が伏字化されてしまわないこと。
        $this->assertSame('plain text', $secretScrubber->scrub('plain text'));
    }
}

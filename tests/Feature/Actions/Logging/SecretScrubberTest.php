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

        // json_encode 経由だと例外オブジェクトは protected/private プロパティのため
        // 中身が {} になり、伏字化の有無に関わらずテスト結果が変わらない（見せかけの保証）。
        // walk() の戻り値を直接アサートする。
        $this->assertIsString($result['url']);
        $this->assertStringNotContainsString('discord.com/api/webhooks/abc/xyz', $result['url']);
        $this->assertStringContainsString('[REDACTED]', $result['url']);

        $this->assertIsArray($result['nested']);
        $this->assertIsString($result['nested']['token']);
        $this->assertStringNotContainsString('ntn_supersecret', $result['nested']['token']);
        $this->assertStringContainsString('[REDACTED]', $result['nested']['token']);

        // Throwable -> string への変換は walk() のドキュメント化された契約（SecretScrubber.php 参照）。
        $this->assertIsString($result['exception']);
        $this->assertStringNotContainsString('ntn_supersecret', $result['exception']);
        $this->assertStringContainsString('[REDACTED]', $result['exception']);
    }

    public function test_scrub_masks_secret_at_minimum_length_boundary(): void
    {
        // secrets() の境界値: mb_strlen >= 5 が伏字化対象の下限（SecretScrubber.php 100行目付近）。
        // 4文字以下は対象外であることは既存の test_scrub_does_not_mangle_common_words_when_secret_is_short
        // で確認済み。ここではちょうど5文字の秘密情報が実際に伏字化されることを確認する。
        Config::set('database.connections.mysql.password', '12345');

        $secretScrubber = new SecretScrubber;

        $result = $secretScrubber->scrub('pass: 12345 end');

        $this->assertStringNotContainsString('12345', $result);
        $this->assertStringContainsString('[REDACTED]', $result);
    }

    public function test_scrub_does_not_mangle_common_words_when_secret_is_short(): void
    {
        Config::set('database.connections.mysql.password', 'root');

        $secretScrubber = new SecretScrubber;

        // "root"(4文字) のような開発環境の短いパスワードは伏字化対象に含めない。
        // chroot/uproot 等の無関係な単語まで破壊してしまうため。
        $this->assertSame('chroot jail for root', $secretScrubber->scrub('chroot jail for root'));
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

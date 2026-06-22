<?php

declare(strict_types=1);

namespace Tests\Feature\Logging;

use App\Actions\Logging\SecretScrubber;
use App\Logging\RedactSecretsProcessor;
use Illuminate\Support\Facades\Config;
use Monolog\DateTimeImmutable;
use Monolog\Level;
use Monolog\LogRecord;
use Tests\Feature\TestCase;

/**
 * C3 / B4: ファイルログ書き込み直前に機密値が伏字化されること。
 */
final class RedactSecretsProcessorTest extends TestCase
{
    public function test_processor_scrubs_message_and_context(): void
    {
        Config::set('services.notion.secret', 'ntn_supersecret');

        $redactSecretsProcessor = new RedactSecretsProcessor(new SecretScrubber);

        $logRecord = new LogRecord(
            datetime: new DateTimeImmutable(true),
            channel: 'daily',
            level: Level::Error,
            message: 'failed with ntn_supersecret',
            context: ['detail' => 'token=ntn_supersecret'],
            extra: [],
        );

        $result = $redactSecretsProcessor($logRecord);

        $this->assertStringNotContainsString('ntn_supersecret', $result->message);
        $this->assertStringNotContainsString('ntn_supersecret', (string) json_encode($result->context));
        $this->assertStringContainsString('[REDACTED]', $result->message);
    }
}

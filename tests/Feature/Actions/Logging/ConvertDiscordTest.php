<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Logging;

use App\Actions\Logging\ConvertDiscord;
use App\Actions\Logging\SecretScrubber;
use Illuminate\Support\Facades\Config;
use MarvinLabs\DiscordLogger\Discord\Message;
use Tests\Feature\TestCase;

/**
 * C3 / B4: Discord 送出経路の全体（addMessageContent + addMessageStacktrace）を通して
 * 機密値が漏れないこと。
 */
final class ConvertDiscordTest extends TestCase
{
    public function test_stacktrace_is_extracted_even_though_context_gets_scrubbed(): void
    {
        Config::set('services.notion.secret', 'ntn_supersecret');

        $convertDiscord = new ConvertDiscord(app('config'), new SecretScrubber);
        $message = Message::make();

        $this->callProtected($convertDiscord, 'addMessageContent', [$message, $this->record()]);

        // スタックトレースが取得・添付されていること（先に scrub していたら null になっていたはず）。
        $this->assertNotNull($message->file);
        $this->assertStringNotContainsString('ntn_supersecret', (string) ($message->content ?? ''));
    }

    public function test_inherited_add_message_stacktrace_does_not_overwrite_with_raw_trace(): void
    {
        $convertDiscord = new ConvertDiscord(app('config'), new SecretScrubber);
        $message = Message::make();
        $message->file('[REDACTED-SENTINEL]', 'f.txt');

        // 親クラスの addMessageStacktrace が未伏字化の生トレースで上書きしないこと（no-op であること）。
        $this->callProtected($convertDiscord, 'addMessageStacktrace', [$message, $this->record()]);

        $this->assertSame('[REDACTED-SENTINEL]', $message->file['contents']);
    }

    private function record(): array
    {
        return [
            'datetime' => new \DateTime,
            'level_name' => 'ERROR',
            'message' => 'sync failed with ntn_supersecret',
            'context' => [
                'exception' => new \RuntimeException('boom'),
            ],
        ];
    }

    private function callProtected(object $object, string $method, array $args): mixed
    {
        $reflectionMethod = new \ReflectionMethod($object, $method);

        return $reflectionMethod->invoke($object, ...$args);
    }
}

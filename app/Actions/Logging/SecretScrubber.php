<?php

declare(strict_types=1);

namespace App\Actions\Logging;

use Illuminate\Support\Facades\Config;

/**
 * ログ・例外レポート・Discord 送出に機密値が混入するのを防ぐための伏字化。
 *
 * 例外メッセージやスタックトレース、Guzzle 例外に含まれる Authorization ヘッダ等に
 * 紛れ込んだ既知の機密値（Notion シークレット、Discord Webhook URL、DB パスワード）を
 * 送出直前に [REDACTED] へ置換する予防制御。
 */
final class SecretScrubber
{
    private const string MASK = '[REDACTED]';

    public function scrub(string $value): string
    {
        $secrets = $this->secrets();
        if ($secrets === []) {
            return $value;
        }

        return str_replace($secrets, self::MASK, $value);
    }

    /**
     * @param  array<array-key,mixed>  $context
     * @return array<array-key,mixed>
     */
    public function scrubArray(array $context): array
    {
        $secrets = $this->secrets();
        if ($secrets === []) {
            return $context;
        }

        /** @var array<array-key,mixed> $scrubbed */
        $scrubbed = $this->walk($context, $secrets);

        return $scrubbed;
    }

    /**
     * @param  list<string>  $secrets
     */
    private function walk(mixed $value, array $secrets): mixed
    {
        if (is_string($value)) {
            return str_replace($secrets, self::MASK, $value);
        }

        if (is_array($value)) {
            return array_map(fn (mixed $item): mixed => $this->walk($item, $secrets), $value);
        }

        if ($value instanceof \Throwable) {
            // 例外オブジェクトはメッセージ + トレース文字列に展開してから伏字化する。
            return str_replace($secrets, self::MASK, (string) $value);
        }

        return $value;
    }

    /**
     * 伏字化対象の機密値一覧（空値は対象から除外する）。
     *
     * @return list<string>
     */
    private function secrets(): array
    {
        $candidates = [
            (string) Config::string('services.notion.secret', ''),
            (string) Config::string('logging.channels.discord.url', ''),
            (string) Config::string('database.connections.mysql.password', ''),
            (string) Config::string('database.connections.portal.password', ''),
        ];

        return array_values(array_unique(array_filter(
            $candidates,
            fn (string $value): bool => $value !== '',
        )));
    }
}

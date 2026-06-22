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

    /**
     * @var list<string>|null
     */
    private ?array $cachedSecrets = null;

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
            // 注意: これにより context['exception'] は Throwable から string に変わる。
            // Sentry 等、Throwable のままであることを期待する Monolog processor/handler を
            // 将来追加する場合は、本プロセッサより前段に置くこと。
            return str_replace($secrets, self::MASK, (string) $value);
        }

        return $value;
    }

    /**
     * 伏字化対象の機密値一覧（短すぎる値・空値は誤爆防止のため対象から除外する）。
     * リクエスト中に変わらない値なのでインスタンス単位でキャッシュする
     * （静的キャッシュにすると PHPUnit のテスト間で Config 変更が反映されなくなるため避ける）。
     *
     * @return list<string>
     */
    private function secrets(): array
    {
        if ($this->cachedSecrets !== null) {
            return $this->cachedSecrets;
        }

        $candidates = [
            Config::get('services.notion.secret'),
            Config::get('logging.channels.discord.url'),
            Config::get('database.connections.mysql.password'),
            Config::get('database.connections.portal.password'),
        ];

        return $this->cachedSecrets = array_values(array_unique(array_filter(
            $candidates,
            // 未設定(null)や開発環境の "root"(4文字) 等の短い値まで伏字化すると
            // chroot/uproot 等の無関係な単語まで壊してしまうため、5文字未満は対象外にする。
            fn (mixed $value): bool => is_string($value) && mb_strlen($value) >= 5,
        )));
    }
}

<?php

declare(strict_types=1);

namespace App\Actions\Extract;

use App\Models\RawPage;
use Carbon\CarbonImmutable;
use Psr\Log\LoggerInterface;

/**
 * 抽出失敗時の共通処理。
 *
 * 以前は失敗時に RawPage を delete() していたが、一過性のエラーでも唯一の
 * スクレイプ結果が恒久消失してしまうため、削除をやめて失敗を隔離・可視化する。
 * 次回以降のリトライで成功すればフラグはクリアされ自己回復する。
 */
final class MarkExtractFailed
{
    public function __invoke(LoggerInterface $logger, RawPage $rawPage, \Throwable $throwable): void
    {
        $logger->error('failed', [$rawPage->url, $throwable]);
        $rawPage->update(['extract_failed_at' => CarbonImmutable::now()]);
    }

    /**
     * 抽出が成功したら過去の失敗フラグをクリアする（自己回復）。
     */
    public function clear(RawPage $rawPage): void
    {
        if ($rawPage->extract_failed_at !== null) {
            $rawPage->update(['extract_failed_at' => null]);
        }
    }
}

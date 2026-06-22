<?php

declare(strict_types=1);

namespace App\Logging;

use App\Actions\Logging\SecretScrubber;
use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;

/**
 * ファイルログ（daily / api チャンネル）に書き出す直前に機密値を伏字化する Monolog プロセッサ。
 * Discord 送出経路は ConvertDiscord 側で別途スクラブする。
 */
final readonly class RedactSecretsProcessor implements ProcessorInterface
{
    public function __construct(private SecretScrubber $secretScrubber) {}

    public function __invoke(LogRecord $record): LogRecord
    {
        return $record->with(
            message: $this->secretScrubber->scrub($record->message),
            context: $this->secretScrubber->scrubArray($record->context),
            extra: $this->secretScrubber->scrubArray($record->extra),
        );
    }
}

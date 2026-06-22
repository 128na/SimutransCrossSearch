<?php

declare(strict_types=1);

namespace App\Logging;

use App\Actions\Logging\SecretScrubber;
use Illuminate\Log\Logger;
use Monolog\Logger as MonologLogger;

/**
 * ログチャンネルに機密値伏字化プロセッサを差し込む tap。
 * config/logging.php の対象チャンネルの 'tap' に登録する。
 */
final class RedactSecretsTap
{
    public function __invoke(Logger $logger): void
    {
        $monolog = $logger->getLogger();
        if ($monolog instanceof MonologLogger) {
            $monolog->pushProcessor(
                new RedactSecretsProcessor(app(SecretScrubber::class)),
            );
        }
    }
}

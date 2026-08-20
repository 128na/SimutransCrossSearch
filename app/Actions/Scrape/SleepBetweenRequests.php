<?php

declare(strict_types=1);

namespace App\Actions\Scrape;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Sleep;

final class SleepBetweenRequests
{
    public function __invoke(?\Throwable $throwable, int $intervalSeconds, int $rateLimitCooldownSeconds): void
    {
        // 429 は通常のリトライ間隔では解消しないため、長めに待って次の URL へ進む。
        if ($throwable instanceof RequestException && $throwable->response->status() === 429) {
            Sleep::for($rateLimitCooldownSeconds)->seconds();

            return;
        }

        Sleep::for($intervalSeconds)->seconds();
    }
}

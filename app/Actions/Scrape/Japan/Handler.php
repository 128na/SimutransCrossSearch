<?php

declare(strict_types=1);

namespace App\Actions\Scrape\Japan;

use App\Actions\Scrape\FetchHtml;
use App\Actions\Scrape\HandlerInterface;
use App\Actions\Scrape\UpdateOrCreateRawPage;
use App\Enums\Encoding;
use App\Enums\SiteName;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Sleep;
use Psr\Log\LoggerInterface;

final readonly class Handler implements HandlerInterface
{
    private const int IntervalSeconds = 2;

    private const int RateLimitCooldownSeconds = 15;

    public function __construct(
        private FetchHtml $fetchHtml,
        private FindUrls $findUrls,
        private UpdateOrCreateRawPage $updateOrCreateRawPage,
    ) {}

    #[\Override]
    public function __invoke(LoggerInterface $logger): void
    {
        $urls = ($this->findUrls)();

        foreach ($urls as $url) {
            try {
                $logger->info('try', [$url]);
                $html = ($this->fetchHtml)($url, Encoding::EUC_JP)->outerHtml();
                ($this->updateOrCreateRawPage)(
                    $url,
                    SiteName::Japan,
                    $html
                );
                Sleep::for(self::IntervalSeconds)->seconds();
            } catch (\Throwable $th) {
                $logger->error('failed', [$url, $th]);
                $this->sleepAfterFailure($th);
            }
        }
    }

    private function sleepAfterFailure(\Throwable $throwable): void
    {
        // 429 は通常のリトライ間隔では解消しないため、長めに待って次の URL へ進む。
        if ($throwable instanceof RequestException && $throwable->response->status() === 429) {
            Sleep::for(self::RateLimitCooldownSeconds)->seconds();

            return;
        }

        Sleep::for(self::IntervalSeconds)->seconds();
    }
}

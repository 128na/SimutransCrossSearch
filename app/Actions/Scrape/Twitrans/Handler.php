<?php

declare(strict_types=1);

namespace App\Actions\Scrape\Twitrans;

use App\Actions\Scrape\FetchHtml;
use App\Actions\Scrape\HandlerInterface;
use App\Actions\Scrape\SleepBetweenRequests;
use App\Actions\Scrape\UpdateOrCreateRawPage;
use App\Enums\Encoding;
use App\Enums\SiteName;
use Psr\Log\LoggerInterface;

final readonly class Handler implements HandlerInterface
{
    private const int INTERVAL_SECONDS = 10;

    private const int RATE_LIMIT_COOLDOWN_SECONDS = 60;

    public function __construct(
        private FetchHtml $fetchHtml,
        private FindUrls $findUrls,
        private UpdateOrCreateRawPage $updateOrCreateRawPage,
        private SleepBetweenRequests $sleepBetweenRequests,
    ) {}

    #[\Override]
    public function __invoke(LoggerInterface $logger): void
    {
        $urls = ($this->findUrls)();

        foreach ($urls as $url) {
            try {
                $logger->info('try', [$url]);
                $html = ($this->fetchHtml)($url, Encoding::UTF_8)->outerHtml();
                ($this->updateOrCreateRawPage)(
                    $url,
                    SiteName::Twitrans,
                    $html
                );
                ($this->sleepBetweenRequests)(null, self::INTERVAL_SECONDS, self::RATE_LIMIT_COOLDOWN_SECONDS);
            } catch (\Throwable $th) {
                $logger->error('failed', [$url, $th]);
                ($this->sleepBetweenRequests)($th, self::INTERVAL_SECONDS, self::RATE_LIMIT_COOLDOWN_SECONDS);
            }
        }
    }
}

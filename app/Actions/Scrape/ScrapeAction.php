<?php

declare(strict_types=1);

namespace App\Actions\Scrape;

use App\Enums\SiteName;
use Psr\Log\LoggerInterface;

final readonly class ScrapeAction
{
    public function __construct(
        private HandlerFactory $handlerFactory
    ) {}

    public function __invoke(?SiteName $siteName, LoggerInterface $logger): void
    {
        $siteNames = $siteName instanceof SiteName ? [$siteName] : SiteName::cases();

        $failure = null;

        foreach ($this->handlerFactory->create($siteNames) as $index => $handler) {
            try {
                $handler($logger);
            } catch (\Throwable $th) {
                $logger->error('site failed', [$siteNames[$index]->value, $th]);
                $failure ??= $th;
            }
        }

        // 全サイトを試行した後で改めて投げ直す。呼び出し元(ScrapeCommand)の
        // report()/終了コード/last_crawl 更新スキップが、1サイトの失敗でも
        // 引き続き働くようにするため。
        if ($failure instanceof \Throwable) {
            throw $failure;
        }
    }
}

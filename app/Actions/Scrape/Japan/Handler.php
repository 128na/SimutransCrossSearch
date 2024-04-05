<?php

declare(strict_types=1);

namespace App\Actions\Scrape\Japan;

use App\Actions\Scrape\FetchHtml;
use App\Actions\Scrape\HandlerInterface;
use App\Actions\Scrape\UpdateOrCreateRawPage;
use App\Enums\Encoding;
use App\Enums\SiteName;
use Illuminate\Support\Sleep;
use Psr\Log\LoggerInterface;

class Handler implements HandlerInterface
{
    public function __construct(
        private readonly FetchHtml $fetchHtml,
        private readonly FindUrls $findUrls,
        private readonly UpdateOrCreateRawPage $updateOrCreateRawPage,
    ) {
    }

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
                Sleep::for(1)->second();
            } catch (\Throwable $th) {
                $logger->error('failed', [$url, $th]);
            }
        }
    }
}

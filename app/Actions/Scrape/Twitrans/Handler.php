<?php

declare(strict_types=1);

namespace App\Actions\Scrape\Twitrans;

use App\Actions\Scrape\FetchHtml;
use App\Actions\Scrape\HandlerInterface;
use App\Actions\Scrape\UpdateOrCreateRawPage;
use App\Enums\SiteName;
use Illuminate\Log\Logger;
use Illuminate\Support\Sleep;

class Handler implements HandlerInterface
{
    public function __construct(
        private readonly FetchHtml $fetchHtml,
        private readonly FindUrls $findUrls,
        private readonly UpdateOrCreateRawPage $updateOrCreateRawPage,
    ) {
    }

    public function __invoke(Logger $logger): void
    {
        $urls = ($this->findUrls)();

        foreach ($urls as $url) {
            try {
                $logger->info('try', [$url]);
                $html = ($this->fetchHtml)($url, 'EUC-JP')->outerHtml();
                ($this->updateOrCreateRawPage)(
                    $url,
                    SiteName::Twitrans,
                    $html
                );
                Sleep::for(1)->second();
            } catch (\Throwable $th) {
                $logger->error('failed', [$url, $th]);
            }
        }
    }
}

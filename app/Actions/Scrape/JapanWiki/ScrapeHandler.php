<?php

declare(strict_types=1);

namespace App\Actions\Scrape\JapanWiki;

use App\Actions\Scrape\FetchHtml;
use App\Actions\Scrape\ScrapeHandlerInterface;
use App\Actions\Scrape\UpdateOrCreateRawPage;
use App\Enums\SiteName;

class ScrapeHandler implements ScrapeHandlerInterface
{
    public function __construct(
        private readonly FetchHtml $fetchHtml,
        private readonly FindUrls $findUrls,
        private readonly UpdateOrCreateRawPage $updateOrCreateRawPage,
    ) {
    }

    public function __invoke(): void
    {
        $urls = ($this->findUrls)();
        dd($urls);

        foreach ($urls as $url) {
            try {
                $html = $this->fetchHtml->request('GET', $url)->outerHtml();
                ($this->updateOrCreateRawPage)(
                    $url,
                    SiteName::SimutransJapanWiki,
                    $html
                );
            } catch (\Throwable $th) {
                report($th);
            }
        }
    }
}

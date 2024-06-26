<?php

declare(strict_types=1);

namespace App\Actions\Scrape\Twitrans;

use App\Actions\Scrape\FetchHtml;
use App\Actions\Scrape\HandlerInterface;
use App\Actions\Scrape\UpdateOrCreateRawPage;
use App\Enums\Encoding;
use App\Enums\SiteName;
use Illuminate\Support\Sleep;
use Psr\Log\LoggerInterface;

final readonly class Handler implements HandlerInterface
{
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
                $html = ($this->fetchHtml)($url, Encoding::UTF_8)->outerHtml();
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

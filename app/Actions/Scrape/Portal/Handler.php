<?php

declare(strict_types=1);

namespace App\Actions\Scrape\Portal;

use App\Actions\Scrape\HandlerInterface;
use App\Actions\Scrape\UpdateOrCreateRawPage;
use App\Enums\SiteName;
use Illuminate\Support\Sleep;
use Psr\Log\LoggerInterface;

final readonly class Handler implements HandlerInterface
{
    public function __construct(
        private AllUrl $allUrl,
        private UpdateOrCreateRawPage $updateOrCreateRawPage,
    ) {}

    #[\Override]
    public function __invoke(LoggerInterface $logger): void
    {
        $urlChunks = ($this->allUrl)()->chunk(100);
        foreach ($urlChunks as $urls) {
            $logger->info('Processing chunk', ['count' => count($urls)]);

            foreach ($urls as $url) {
                try {
                    $logger->info('try', [$url]);
                    ($this->updateOrCreateRawPage)(
                        $url,
                        SiteName::Portal,
                        ''
                    );
                    Sleep::for(100)->millisecond();
                } catch (\Throwable $th) {
                    $logger->error('failed', [$url, $th]);
                }
            }

            $logger->info('Chunk completed', ['count' => count($urls)]);
        }
    }
}

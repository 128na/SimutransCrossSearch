<?php

declare(strict_types=1);

namespace App\Actions\Scrape\Portal;

use App\Actions\Scrape\BulkUpdateOrCreateRawPage;
use App\Actions\Scrape\HandlerInterface;
use App\Enums\SiteName;
use Illuminate\Support\Sleep;
use Psr\Log\LoggerInterface;

final readonly class Handler implements HandlerInterface
{
    public function __construct(
        private AllUrl $allUrl,
        private BulkUpdateOrCreateRawPage $bulkUpdateOrCreateRawPage,
    ) {}

    #[\Override]
    public function __invoke(LoggerInterface $logger): void
    {
        foreach (($this->allUrl)()->chunk(100) as $urls) {
            $logger->info('Processing chunk', ['count' => count($urls)]);

            try {
                /** @var \Illuminate\Support\Collection<int,string> $urls */
                ($this->bulkUpdateOrCreateRawPage)(
                    $urls,
                    SiteName::Portal,
                    ''
                );
                $logger->info('Chunk completed', ['count' => count($urls)]);
            } catch (\Throwable $th) {
                $logger->error('Chunk failed', ['count' => count($urls), 'error' => $th->getMessage()]);
            }

            Sleep::for(100)->millisecond();
        }
    }
}

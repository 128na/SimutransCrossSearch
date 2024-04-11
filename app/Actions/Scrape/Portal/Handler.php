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
        private CursorUrl $cursorUrl,
        private UpdateOrCreateRawPage $updateOrCreateRawPage,
    ) {

    }

    #[\Override]
    public function __invoke(LoggerInterface $logger): void
    {
        foreach (($this->cursorUrl)() as $url) {
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
    }
}

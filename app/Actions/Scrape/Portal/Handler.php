<?php

declare(strict_types=1);

namespace App\Actions\Scrape\Portal;

use App\Actions\Scrape\HandlerInterface;
use App\Actions\Scrape\UpdateOrCreateRawPage;
use App\Enums\SiteName;
use Illuminate\Log\Logger;
use Illuminate\Support\Sleep;

class Handler implements HandlerInterface
{
    public function __construct(
        private readonly CursorUrl $cursorUrl,
        private readonly UpdateOrCreateRawPage $updateOrCreateRawPage,
    ) {

    }

    public function __invoke(Logger $logger): void
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

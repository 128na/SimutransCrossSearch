<?php

declare(strict_types=1);

namespace App\Actions\Extract\Twitrans;

use App\Actions\Extract\ChunkRawPages;
use App\Actions\Extract\HandlerInterface;
use App\Actions\Extract\SyncPak;
use App\Actions\Extract\UpdateOrCreatePage;
use App\Enums\SiteName;
use App\Models\Page;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Psr\Log\LoggerInterface;

class Handler implements HandlerInterface
{
    public function __construct(
        private readonly ChunkRawPages $chunkRawPages,
        private readonly ExtractLastModified $extractLastModified,
        private readonly ExtractContents $extractContents,
        private readonly UpdateOrCreatePage $updateOrCreatePage,
        private readonly SyncPak $syncPak,
    ) {
    }

    public function __invoke(LoggerInterface $logger): void
    {
        ($this->chunkRawPages)(SiteName::Twitrans, function (Collection $rawPages) use ($logger): void {
            /**
             * @var \Illuminate\Support\Collection<int,\App\Models\RawPage> $rawPages
             */
            foreach ($rawPages as $rawPage) {
                try {
                    $logger->info('try', [$rawPage->url]);
                    $lastModiefied = ($this->extractLastModified)($rawPage);
                    // pageがない
                    // pageがあって更新有り
                    if (! $rawPage->page || $this->needUpdate($rawPage->page, $lastModiefied)) {
                        $contents = ($this->extractContents)($rawPage);
                        $page = ($this->updateOrCreatePage)(
                            $rawPage,
                            $contents['title'],
                            $contents['text'],
                            $lastModiefied
                        );

                        ($this->syncPak)($page, $contents['paks']);
                    }
                } catch (\Throwable $th) {
                    $logger->error('failed', [$rawPage->url, $th]);
                }
            }
        });
    }

    private function needUpdate(Page $page, CarbonImmutable $lastModiefied): bool
    {
        return $lastModiefied->greaterThan($page->last_modified);
    }
}

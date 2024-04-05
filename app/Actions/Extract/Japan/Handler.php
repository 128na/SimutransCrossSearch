<?php

declare(strict_types=1);

namespace App\Actions\Extract\Japan;

use App\Actions\Extract\ChunkRawPages;
use App\Actions\Extract\HandlerInterface;
use App\Actions\Extract\SyncPak;
use App\Actions\Extract\UpdateOrCreatePage;
use App\Enums\SiteName;
use App\Models\Page;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;

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

    public function __invoke(): void
    {
        ($this->chunkRawPages)(SiteName::Japan, function (Collection $rawPages): void {
            /**
             * @var \Illuminate\Support\Collection<int,\App\Models\RawPage> $rawPages
             */
            foreach ($rawPages as $rawPage) {
                $lastModiefied = ($this->extractLastModified)($rawPage);
                // pageがない
                // pageがあって更新有り
                if (! $rawPage->page || $this->needUpdate($rawPage->page, $lastModiefied)) {
                    $contents = ($this->extractContents)($rawPage);
                    dd($contents);
                    $page = ($this->updateOrCreatePage)(
                        $rawPage->id,
                        $rawPage->url,
                        $rawPage->site_name,
                        $contents['title'],
                        $contents['text'],
                        $lastModiefied
                    );

                    ($this->syncPak)($page, $contents['paks']);
                }
            }
        });
    }

    private function needUpdate(Page $page, CarbonImmutable $lastModiefied): bool
    {
        return $lastModiefied->greaterThan($page->last_modified);
    }
}

<?php

declare(strict_types=1);

namespace App\Actions\Extract\Japan;

use App\Actions\Extract\ChunkRawPages;
use App\Actions\Extract\HandlerInterface;
use App\Actions\Extract\MarkExtractFailed;
use App\Actions\Extract\UpdateOrCreatePageWithPaks;
use App\Enums\SiteName;
use App\Models\Page;
use App\Models\RawPage;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Psr\Log\LoggerInterface;

final readonly class Handler implements HandlerInterface
{
    public function __construct(
        private ChunkRawPages $chunkRawPages,
        private ExtractLastModified $extractLastModified,
        private ExtractContents $extractContents,
        private UpdateOrCreatePageWithPaks $updateOrCreatePageWithPaks,
        private MarkExtractFailed $markExtractFailed,
    ) {}

    #[\Override]
    public function __invoke(LoggerInterface $logger): void
    {
        ($this->chunkRawPages)(SiteName::Japan, function (Collection $rawPages) use ($logger): void {
            /**
             * @var Collection<int,RawPage> $rawPages
             */
            foreach ($rawPages as $rawPage) {
                try {
                    $logger->info('try', [$rawPage->url]);
                    $lastModiefied = ($this->extractLastModified)($rawPage);
                    // pageがない
                    // pageがあって更新有り
                    if (! $rawPage->page || $this->needUpdate($rawPage->page, $lastModiefied)) {
                        $contents = ($this->extractContents)($rawPage);
                        ($this->updateOrCreatePageWithPaks)(
                            $rawPage,
                            $contents['title'],
                            $contents['text'],
                            $lastModiefied,
                            $contents['paks']
                        );
                    }

                    $this->markExtractFailed->clear($rawPage);
                } catch (\Throwable $th) {
                    ($this->markExtractFailed)($logger, $rawPage, $th);
                }
            }
        });
    }

    private function needUpdate(Page $page, CarbonImmutable $lastModiefied): bool
    {
        return $lastModiefied->greaterThan($page->last_modified);
    }
}

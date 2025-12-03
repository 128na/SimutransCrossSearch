<?php

declare(strict_types=1);

namespace App\Actions\Extract\Portal;

use App\Actions\Extract\ChunkRawPages;
use App\Actions\Extract\HandlerInterface;
use App\Actions\Extract\SyncPak;
use App\Actions\Extract\UpdateOrCreatePage;
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
        private FindArticle $findArticle,
        private ExtractContents $extractContents,
        private UpdateOrCreatePage $updateOrCreatePage,
        private SyncPak $syncPak,
    ) {}

    #[\Override]
    public function __invoke(LoggerInterface $logger): void
    {
        ($this->chunkRawPages)(SiteName::Portal, function (Collection $rawPages) use ($logger): void {
            /**
             * @var \Illuminate\Support\Collection<int,\App\Models\RawPage> $rawPages
             */
            foreach ($rawPages as $rawPage) {
                try {
                    $logger->info('try', [$rawPage->url]);
                    $article = ($this->findArticle)($this->getPortalArticleId($rawPage));
                    /** @var CarbonImmutable */
                    $lastModiefied = $article->modified_at;
                    // pageがない
                    // pageがあって更新有り
                    if (! $rawPage->page || $this->needUpdate($rawPage->page, $lastModiefied)) {
                        $contents = ($this->extractContents)($article);
                        $page = ($this->updateOrCreatePage)(
                            $rawPage,
                            $contents['title'],
                            $contents['text'],
                            $lastModiefied,
                        );

                        ($this->syncPak)($page, $contents['paks']);
                    }
                } catch (\Throwable $th) {
                    $logger->error('failed', [$rawPage->url, $th]);
                    $rawPage->delete();
                }
            }
        });
    }

    private function needUpdate(Page $page, CarbonImmutable $lastModiefied): bool
    {
        return $lastModiefied->greaterThan($page->last_modified);
    }

    private function getPortalArticleId(RawPage $rawPage): string
    {
        $tmp = explode('/', (string) $rawPage->url);

        return end($tmp) ?: '';
    }
}

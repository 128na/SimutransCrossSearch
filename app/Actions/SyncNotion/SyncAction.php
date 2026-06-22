<?php

declare(strict_types=1);

namespace App\Actions\SyncNotion;

use App\Models\Page;
use App\Models\Pak;
use Illuminate\Support\Collection;
use Notion\Databases\Database;
use Notion\Databases\Properties\SelectOption;
use Notion\Notion;
use Notion\Pages\Page as NotionPage;
use Notion\Pages\PageParent;
use Notion\Pages\Properties\Date;
use Notion\Pages\Properties\MultiSelect;
use Notion\Pages\Properties\RichTextProperty;
use Notion\Pages\Properties\Url;

final readonly class SyncAction
{
    private const array PAGE_PROP_MAPPING = [
        'title' => 'タイトル',
        'site_name' => 'サイト名',
        'last_modified' => '最終更新日時',
        'url' => 'URL',
        'paks' => 'パックセット',
    ];

    public function __construct(private Notion $notion) {}

    public function __invoke(string $databaseId, int $limit): void
    {
        $database = $this->notion->databases()->find($databaseId);
        $notionPages = collect($this->notion->databases()->queryAllPages($database));

        $pages = Page::query()->with('paks')->orderBy('last_modified', 'desc')->limit($limit)->get();

        // 1 件の Notion API エラーでバッチ全体を止めず、項目単位で隔離して継続する。
        $failed = $this->deleteOldNotionPages($pages, $notionPages)
            + $this->addNewNotionPages($database, $pages, $notionPages);

        if ($failed > 0) {
            logger()->error('[NotionService] sync completed with failures', ['failed' => $failed]);
        }
    }

    /**
     * @param  Collection<int,Page>  $pages
     * @param  Collection<int,NotionPage>  $notionPages
     * @return int 失敗件数
     */
    private function deleteOldNotionPages(Collection $pages, Collection $notionPages): int
    {
        $failed = 0;
        $pagesByUrl = $pages->keyBy('url');
        foreach ($notionPages as $notionPage) {
            $url = null;
            try {
                $url = $this->getUrlProp($notionPage);
                if ($url === null || ! $pagesByUrl->has($url)) {
                    logger('[NotionService]delete', ['url' => $url]);
                    $this->notion->pages()->delete($notionPage);
                }
            } catch (\Throwable $th) {
                $failed++;
                logger()->error('[NotionService] delete failed', ['url' => $url, $th]);
            }
        }

        return $failed;
    }

    /**
     * @param  Collection<int,Page>  $pages
     * @param  Collection<int,NotionPage>  $notionPages
     * @return int 失敗件数
     */
    private function addNewNotionPages(Database $database, Collection $pages, Collection $notionPages): int
    {
        $options = $this->getOptions($database);
        $failed = 0;
        foreach ($pages as $page) {
            try {
                $exists = true;
                $url = $page->url;
                $np = $notionPages->first(fn (NotionPage $notionPage): bool => $this->getUrlProp($notionPage) === $url);
                if (! $np) {
                    $exists = false;
                    $np = NotionPage::create(PageParent::database($database->id));
                }

                $np = $np->changeTitle($page->title)
                    ->addProperty(
                        self::PAGE_PROP_MAPPING['url'],
                        Url::create($page->url)
                    )
                    ->addProperty(
                        self::PAGE_PROP_MAPPING['site_name'],
                        RichTextProperty::fromString(__('misc.'.$page->site_name->value))
                    )
                    ->addProperty(
                        self::PAGE_PROP_MAPPING['last_modified'],
                        Date::create($page->last_modified->toDateTimeImmutable())
                    )
                    ->addProperty(
                        self::PAGE_PROP_MAPPING['paks'],
                        MultiSelect::fromOptions(
                            ...$page
                                ->paks
                                ->map(fn (Pak $pak): array|string => __('misc.'.$pak->slug->value))
                                ->filter(fn (array|string $name): bool => is_string($name))
                                ->map(fn (string $name) => $options->first(fn ($opt): bool => $name === $opt->name))
                                ->filter(fn ($pak): bool => ! is_null($pak))
                        )
                    );

                if ($exists) {
                    logger('[NotionService] update', ['url' => $url]);
                    $this->notion->pages()->update($np);
                } else {
                    logger('[NotionService] create', ['url' => $url]);
                    $this->notion->pages()->create($np);
                }
            } catch (\Throwable $th) {
                $failed++;
                logger()->error('[NotionService] sync failed', ['url' => $page->url, $th]);
            }
        }

        return $failed;
    }

    private function getUrlProp(NotionPage $notionPage): ?string
    {
        $property = $notionPage->getProperty(self::PAGE_PROP_MAPPING['url']);
        assert($property instanceof Url);

        return $property->url;
    }

    /**
     * @return Collection<int,SelectOption>
     */
    private function getOptions(Database $database): Collection
    {
        $property = $database->properties()->get(self::PAGE_PROP_MAPPING['paks']);
        assert($property instanceof \Notion\Databases\Properties\MultiSelect);

        return collect($property->options);
    }
}

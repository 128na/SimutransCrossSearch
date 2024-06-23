<?php

declare(strict_types=1);

namespace App\Actions\SyncNotion;

use App\Models\Page;
use App\Models\Pak;
use Illuminate\Support\Collection;
use Notion\Databases\Database;
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

        $this->deleteOldNotionPages($pages, $notionPages);
        $this->addNewNotionPages($database, $pages, $notionPages);
    }

    /**
     * @param  Collection<int,Page>  $pages
     * @param  Collection<int,NotionPage>  $notionPages
     */
    private function deleteOldNotionPages(Collection $pages, Collection $notionPages): void
    {
        foreach ($notionPages as $notionPage) {
            $url = $this->getUrlProp($notionPage);
            $page = $pages->first(fn (Page $page): bool => $page->url === $url);
            if (! $page) {
                logger('[NotionService]delete', ['url' => $url]);
                $this->notion->pages()->delete($notionPage);
            }
        }
    }

    /**
     * @param  Collection<int,Page>  $pages
     * @param  Collection<int,NotionPage>  $notionPages
     */
    private function addNewNotionPages(Database $database, Collection $pages, Collection $notionPages): void
    {
        $options = $this->getOptions($database);
        foreach ($pages as $page) {
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
                    MultiSelect::fromOptions(...$page
                        ->paks
                        ->map(fn (Pak $pak) => __('misc.'.$pak->slug->value))
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
        }
    }

    private function getUrlProp(NotionPage $notionPage): ?string
    {
        $property = $notionPage->getProperty(self::PAGE_PROP_MAPPING['url']);
        assert($property instanceof \Notion\Pages\Properties\Url);

        return $property->url;
    }

    /**
     * @return Collection<int,\Notion\Databases\Properties\SelectOption>
     */
    private function getOptions(Database $database): Collection
    {
        $property = $database->properties()->get(self::PAGE_PROP_MAPPING['paks']);
        assert($property instanceof \Notion\Databases\Properties\MultiSelect);

        return collect($property->options);
    }
}

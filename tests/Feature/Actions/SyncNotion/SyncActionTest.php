<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\SyncNotion;

use App\Actions\SyncNotion\SyncAction;
use App\Enums\PakSlug;
use App\Enums\SiteName;
use App\Models\Page;
use App\Models\Pak;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Notion\Databases\Database;
use Notion\Notion;
use Notion\Pages\Page as NotionPage;
use Notion\Pages\PageParent;
use Notion\Pages\Properties\Url;
use Tests\Feature\TestCase;

final class SyncActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_sync_action_creates_updates_and_deletes_pages(): void
    {
        $pak = Pak::factory()->create(['slug' => PakSlug::Pak128]);

        // This page should be created in Notion
        $pageToCreate = Page::factory()->create([
            'title' => 'New Addon',
            'site_name' => SiteName::Japan,
            'url' => 'https://example.com/new',
            'last_modified' => now(),
        ]);
        $pageToCreate->paks()->attach($pak);

        // This page exists in both DB and Notion, should be updated
        Page::factory()->create([
            'title' => 'Updated Addon',
            'site_name' => SiteName::Japan,
            'url' => 'https://example.com/update',
            'last_modified' => now(),
        ]);

        $database = Database::fromArray([
            'id' => 'test_database_id',
            'created_time' => '2023-01-01T00:00:00.000Z',
            'last_edited_time' => '2023-01-01T00:00:00.000Z',
            'title' => [],
            'description' => [],
            'icon' => null,
            'cover' => null,
            'properties' => [
                'Title' => [
                    'id' => 'title',
                    'type' => 'title',
                    'name' => 'Title',
                    'title' => [],
                ],
                'パックセット' => [
                    'id' => 'prop_id',
                    'type' => 'multi_select',
                    'name' => 'パックセット',
                    'multi_select' => [
                        'options' => [
                            ['name' => '128', 'id' => 'opt_128', 'color' => 'default'],
                        ],
                    ],
                ],
            ],
            'parent' => ['type' => 'workspace', 'workspace' => true],
            'url' => 'https://notion.so',
            'is_inline' => false,
        ]);

        $notionPageToUpdate = NotionPage::create(PageParent::database('test_database_id'));
        $notionPageToUpdate = $notionPageToUpdate->addProperty('URL', Url::create('https://example.com/update'));

        $notionPageToDelete = NotionPage::create(PageParent::database('test_database_id'));
        $notionPageToDelete = $notionPageToDelete->addProperty('URL', Url::create('https://example.com/delete'));

        $mock = Mockery::mock(Notion::class);
        $mock->shouldReceive('databases->find')->with('test_database_id')->andReturn($database);
        $mock->shouldReceive('databases->queryAllPages')->with($database)->andReturn([$notionPageToUpdate, $notionPageToDelete]);

        $mock->shouldReceive('pages->delete')->once()->with($notionPageToDelete);

        $mock->shouldReceive('pages->update')->once()->with(Mockery::on(fn (NotionPage $notionPage): bool => $notionPage->getProperty('URL')->url === 'https://example.com/update'));

        $mock->shouldReceive('pages->create')->once()->with(Mockery::on(fn (NotionPage $notionPage): bool => $notionPage->getProperty('URL')->url === 'https://example.com/new'));

        $syncAction = new SyncAction($mock);
        $syncAction('test_database_id', 10);
    }

    /**
     * B1: 既に Notion 側に同じ URL のページが存在する場合、再実行で重複作成しない。
     */
    public function test_does_not_create_duplicate_when_page_already_exists_in_notion(): void
    {
        Page::factory()->create([
            'title' => 'Already Synced',
            'site_name' => SiteName::Japan,
            'url' => 'https://example.com/already-synced',
            'last_modified' => now(),
        ]);

        $database = Database::fromArray([
            'id' => 'test_database_id',
            'created_time' => '2023-01-01T00:00:00.000Z',
            'last_edited_time' => '2023-01-01T00:00:00.000Z',
            'title' => [],
            'description' => [],
            'icon' => null,
            'cover' => null,
            'properties' => [
                'Title' => ['id' => 'title', 'type' => 'title', 'name' => 'Title', 'title' => []],
                'パックセット' => [
                    'id' => 'prop_id',
                    'type' => 'multi_select',
                    'name' => 'パックセット',
                    'multi_select' => ['options' => []],
                ],
            ],
            'parent' => ['type' => 'workspace', 'workspace' => true],
            'url' => 'https://notion.so',
            'is_inline' => false,
        ]);

        $existingNotionPage = NotionPage::create(PageParent::database('test_database_id'));
        $existingNotionPage = $existingNotionPage->addProperty('URL', Url::create('https://example.com/already-synced'));

        $mock = Mockery::mock(Notion::class);
        $mock->shouldReceive('databases->find')->with('test_database_id')->andReturn($database);
        $mock->shouldReceive('databases->queryAllPages')->with($database)->andReturn([$existingNotionPage]);

        $mock->shouldReceive('pages->create')->never();
        $mock->shouldReceive('pages->update')->once();

        $syncAction = new SyncAction($mock);
        $syncAction('test_database_id', 10);
    }

    /**
     * B2: 1 件の Notion API エラーでバッチ全体が止まらず、残り項目が処理されること。
     */
    public function test_continues_syncing_when_one_item_fails(): void
    {
        Page::factory()->create([
            'title' => 'Fails',
            'site_name' => SiteName::Japan,
            'url' => 'https://example.com/fail',
            'last_modified' => now()->subDay(),
        ]);
        Page::factory()->create([
            'title' => 'Succeeds',
            'site_name' => SiteName::Japan,
            'url' => 'https://example.com/ok',
            'last_modified' => now(),
        ]);

        $database = Database::fromArray([
            'id' => 'test_database_id',
            'created_time' => '2023-01-01T00:00:00.000Z',
            'last_edited_time' => '2023-01-01T00:00:00.000Z',
            'title' => [],
            'description' => [],
            'icon' => null,
            'cover' => null,
            'properties' => [
                'Title' => ['id' => 'title', 'type' => 'title', 'name' => 'Title', 'title' => []],
                'パックセット' => [
                    'id' => 'prop_id',
                    'type' => 'multi_select',
                    'name' => 'パックセット',
                    'multi_select' => ['options' => []],
                ],
            ],
            'parent' => ['type' => 'workspace', 'workspace' => true],
            'url' => 'https://notion.so',
            'is_inline' => false,
        ]);

        $mock = Mockery::mock(Notion::class);
        $mock->shouldReceive('databases->find')->with('test_database_id')->andReturn($database);
        $mock->shouldReceive('databases->queryAllPages')->with($database)->andReturn([]);

        // 1 件目（fail）は Notion API がエラーを投げる。
        $mock->shouldReceive('pages->create')
            ->with(Mockery::on(fn (NotionPage $notionPage): bool => $notionPage->getProperty('URL')->url === 'https://example.com/fail'))
            ->andThrow(new \RuntimeException('rate limited'));

        // 2 件目（ok）は 1 件目の失敗後も必ず処理される。
        $mock->shouldReceive('pages->create')
            ->once()
            ->with(Mockery::on(fn (NotionPage $notionPage): bool => $notionPage->getProperty('URL')->url === 'https://example.com/ok'));

        $syncAction = new SyncAction($mock);

        // バッチ全体が中断せず正常終了すること（例外が伝播しない）。
        $syncAction('test_database_id', 10);
    }

    /**
     * B2: delete 側でも 1 件の Notion API エラーでバッチ全体が止まらないこと。
     */
    public function test_continues_deleting_when_one_delete_fails(): void
    {
        $database = $this->minimalDatabase();

        $notionPageFails = NotionPage::create(PageParent::database('test_database_id'));
        $notionPageFails = $notionPageFails->addProperty('URL', Url::create('https://example.com/delete-fails'));

        $notionPageSucceeds = NotionPage::create(PageParent::database('test_database_id'));
        $notionPageSucceeds = $notionPageSucceeds->addProperty('URL', Url::create('https://example.com/delete-ok'));

        $mock = Mockery::mock(Notion::class);
        $mock->shouldReceive('databases->find')->with('test_database_id')->andReturn($database);
        $mock->shouldReceive('databases->queryAllPages')->with($database)->andReturn([$notionPageFails, $notionPageSucceeds]);

        $mock->shouldReceive('pages->delete')->once()->with($notionPageFails)->andThrow(new \RuntimeException('rate limited'));
        $mock->shouldReceive('pages->delete')->once()->with($notionPageSucceeds);

        $syncAction = new SyncAction($mock);

        // バッチ全体が中断せず、残りの削除も実行されること（例外が伝播しない）。
        $syncAction('test_database_id', 10);
    }

    /**
     * B2: URL が無い Notion ページ（手動作成の下書き等）は削除対象から除外する。
     */
    public function test_does_not_delete_notion_page_without_url(): void
    {
        $database = $this->minimalDatabase();

        $notionPageWithoutUrl = NotionPage::create(PageParent::database('test_database_id'));
        $notionPageWithoutUrl = $notionPageWithoutUrl->addProperty('URL', Url::createEmpty());

        $mock = Mockery::mock(Notion::class);
        $mock->shouldReceive('databases->find')->with('test_database_id')->andReturn($database);
        $mock->shouldReceive('databases->queryAllPages')->with($database)->andReturn([$notionPageWithoutUrl]);

        $mock->shouldReceive('pages->delete')->never();

        $syncAction = new SyncAction($mock);
        $syncAction('test_database_id', 10);
    }

    private function minimalDatabase(): Database
    {
        return Database::fromArray([
            'id' => 'test_database_id',
            'created_time' => '2023-01-01T00:00:00.000Z',
            'last_edited_time' => '2023-01-01T00:00:00.000Z',
            'title' => [],
            'description' => [],
            'icon' => null,
            'cover' => null,
            'properties' => [
                'Title' => ['id' => 'title', 'type' => 'title', 'name' => 'Title', 'title' => []],
                'パックセット' => [
                    'id' => 'prop_id',
                    'type' => 'multi_select',
                    'name' => 'パックセット',
                    'multi_select' => ['options' => []],
                ],
            ],
            'parent' => ['type' => 'workspace', 'workspace' => true],
            'url' => 'https://notion.so',
            'is_inline' => false,
        ]);
    }
}

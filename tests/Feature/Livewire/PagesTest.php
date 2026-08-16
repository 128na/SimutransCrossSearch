<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Enums\PakSlug;
use App\Enums\SiteName;
use App\Livewire\Pages;
use App\Models\Page;
use App\Models\Pak;
use App\Models\RawPage;
use Livewire\Livewire;
use Tests\Feature\TestCase;

final class PagesTest extends TestCase
{
    public function test_renders_successfully(): void
    {
        Livewire::test(Pages::class)
            ->assertOk();
    }

    public function test_default_state_selects_all_paks_and_sites(): void
    {
        Livewire::test(Pages::class)
            ->assertSet('paks', [
                PakSlug::Pak64->value => true,
                PakSlug::Pak128->value => true,
                PakSlug::Pak128Jp->value => true,
            ])
            ->assertSet('sites', [
                SiteName::Japan->value => true,
                SiteName::Twitrans->value => true,
                SiteName::Portal->value => true,
            ]);
    }

    public function test_condition_update_resets_page_to_one(): void
    {
        Livewire::test(Pages::class)
            ->set('page', 3)
            ->call('onConditionUpdate')
            ->assertSet('page', 1);
    }

    public function test_pagination_links_do_not_point_to_the_livewire_update_endpoint(): void
    {
        // WithPagination除去に伴い、ページネーションリンクの生成元パスをboot()で
        // 明示的に補っている(Livewire::originalPath())。これが無いと、Livewireの
        // アクション経由で再描画した際にリンク先がPOST専用の内部updateエンドポイント
        // になり、通常のリンククリック(GET)が405になる回帰を防ぐテスト。
        $pak = Pak::factory()->create(['slug' => PakSlug::Pak128]);
        // Fakerの乱数urlは60件生成すると衝突しうる(raw_pages/pagesのurlユニーク制約)ため、
        // テストの再現性を優先して明示的にユニークなurlを採番する。
        for ($i = 0; $i < 60; $i++) {
            $page = Page::factory()->create([
                'site_name' => SiteName::Japan,
                'url' => "https://example.test/page-{$i}",
                'raw_page_id' => RawPage::factory()->create(['url' => "https://example.test/raw-{$i}"])->id,
            ]);
            $page->paks()->attach($pak);
        }

        $html = Livewire::test(Pages::class)
            ->call('onConditionUpdate')
            ->html();

        $this->assertMatchesRegularExpression('#href="[^"]*[?&](amp;)?page=2[^"]*"#', $html);
        $this->assertStringNotContainsString('/update?', $html);
        $this->assertStringNotContainsString('/update&amp;', $html);
    }

    public function test_pagination_links_preserve_search_conditions_across_pages(): void
    {
        // 検索キーワード等がページネーションリンク（プレーンな<a href>によるURL遷移）に
        // 引き継がれず、2ページ目に遷移すると検索条件が失われる不具合の回帰テスト。
        $pak = Pak::factory()->create(['slug' => PakSlug::Pak128]);
        for ($i = 0; $i < 60; $i++) {
            $page = Page::factory()->create([
                'site_name' => SiteName::Japan,
                'title' => "Locomotive Addon {$i}",
                'url' => "https://example.test/loco-{$i}",
                'raw_page_id' => RawPage::factory()->create(['url' => "https://example.test/loco-raw-{$i}"])->id,
            ]);
            $page->paks()->attach($pak);
        }

        $html = Livewire::test(Pages::class)
            ->set('keyword', 'Locomotive')
            ->set('paks.'.PakSlug::Pak64->value, false)
            ->call('onConditionUpdate')
            ->html();

        $this->assertMatchesRegularExpression('#href="[^"]*[?&](amp;)?page=2[^"]*"#', $html);
        $this->assertMatchesRegularExpression('#href="[^"]*keyword=Locomotive[^"]*"#', $html);
        $this->assertMatchesRegularExpression('#href="[^"]*paks%5B'.PakSlug::Pak64->value.'%5D=0[^"]*"#', $html);
    }

    public function test_clear_resets_keyword_paks_sites_and_page(): void
    {
        Livewire::test(Pages::class)
            ->set('keyword', 'foo')
            ->set('paks.'.PakSlug::Pak64->value, false)
            ->set('sites.'.SiteName::Japan->value, false)
            ->set('page', 3)
            ->call('clear')
            ->assertSet('keyword', '')
            ->assertSet('page', 1)
            ->assertSet('paks.'.PakSlug::Pak64->value, true)
            ->assertSet('sites.'.SiteName::Japan->value, true);
    }

    public function test_negative_page_is_clamped_to_one(): void
    {
        $results = Livewire::test(Pages::class)
            ->set('page', -1)
            ->instance()
            ->pages;

        $this->assertSame(1, $results->currentPage());
    }

    public function test_keyword_over_max_length_fails_validation(): void
    {
        Livewire::test(Pages::class)
            ->set('keyword', str_repeat('a', 21))
            ->assertHasErrors(['keyword' => 'max']);
    }

    public function test_search_results_reflect_keyword_filter(): void
    {
        $pak = Pak::factory()->create(['slug' => PakSlug::Pak128]);

        $matching = Page::factory()->create([
            'site_name' => SiteName::Japan,
            'title' => 'Steam Locomotive Addon',
        ]);
        $matching->paks()->attach($pak);

        $other = Page::factory()->create([
            'site_name' => SiteName::Japan,
            'title' => 'Bus Addon',
        ]);
        $other->paks()->attach($pak);

        $testable = Livewire::test(Pages::class)
            ->set('keyword', 'Locomotive');

        $results = $testable->instance()->pages;

        $this->assertCount(1, $results);
        $this->assertSame($matching->id, $results->first()->id);
    }
}

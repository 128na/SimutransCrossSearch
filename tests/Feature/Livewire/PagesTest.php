<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Enums\PakSlug;
use App\Enums\SiteName;
use App\Livewire\Pages;
use App\Models\Page;
use App\Models\Pak;
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

    public function test_clear_resets_keyword_paks_sites_and_page(): void
    {
        Livewire::test(Pages::class)
            ->set('keyword', 'foo')
            ->set('paks.'.PakSlug::Pak64->value, false)
            ->set('page', 3)
            ->call('clear')
            ->assertSet('keyword', '')
            ->assertSet('page', 1)
            ->assertSet('paks.'.PakSlug::Pak64->value, true);
    }

    public function test_keyword_over_max_length_fails_validation(): void
    {
        Livewire::test(Pages::class)
            ->set('keyword', str_repeat('a', 192))
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
            ->set('keyword', 'Locomotive')
            ->call('onConditionUpdate');

        $results = $testable->instance()->pages;

        $this->assertCount(1, $results);
        $this->assertSame($matching->id, $results->first()->id);
    }
}

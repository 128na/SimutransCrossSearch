<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\SearchPage;

use App\Actions\SearchPage\SearchAction;
use App\Enums\PakSlug;
use App\Enums\SiteName;
use App\Models\Page;
use App\Models\Pak;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\TestCase;

final class SearchActionTest extends TestCase
{
    use RefreshDatabase;

    private Pak $pak128;

    private Pak $pak64;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pak128 = Pak::factory()->create(['slug' => PakSlug::Pak128]);
        $this->pak64 = Pak::factory()->create(['slug' => PakSlug::Pak64]);
    }

    public function test_filters_by_site_and_pak(): void
    {
        $page1 = Page::factory()->create(['site_name' => SiteName::Japan]);
        $page1->paks()->attach($this->pak128);

        $page2 = Page::factory()->create(['site_name' => SiteName::Portal]);
        $page2->paks()->attach($this->pak128);

        $page3 = Page::factory()->create(['site_name' => SiteName::Japan]);
        $page3->paks()->attach($this->pak64);

        $searchAction = new SearchAction;

        $result = $searchAction([
            'keyword' => '',
            'sites' => [SiteName::Japan->value],
            'paks' => [PakSlug::Pak128->value],
        ]);

        $this->assertCount(1, $result);
        $this->assertSame($page1->id, $result->first()->id);
    }

    public function test_filters_by_keyword_including_exclude_logic(): void
    {
        $page1 = Page::factory()->create([
            'site_name' => SiteName::Japan,
            'title' => 'Train Addon',
            'text' => 'This is a train',
        ]);
        $page1->paks()->attach($this->pak128);

        $page2 = Page::factory()->create([
            'site_name' => SiteName::Japan,
            'title' => 'Bus Addon',
            'text' => 'This is a bus',
        ]);
        $page2->paks()->attach($this->pak128);

        $page3 = Page::factory()->create([
            'site_name' => SiteName::Japan,
            'title' => 'Electric Train Addon',
            'text' => 'This is an electric train',
        ]);
        $page3->paks()->attach($this->pak128);

        $searchAction = new SearchAction;

        $baseQuery = [
            'sites' => [SiteName::Japan->value],
            'paks' => [PakSlug::Pak128->value],
        ];

        // Search for 'Train'
        $result = $searchAction(array_merge($baseQuery, ['keyword' => 'Train']));
        $this->assertCount(2, $result); // page1, page3

        // Search for 'Train -Electric'
        $result = $searchAction(array_merge($baseQuery, ['keyword' => 'Train -Electric']));
        $this->assertCount(1, $result); // page1 only

        // Search for 'Bus'
        $result = $searchAction(array_merge($baseQuery, ['keyword' => 'Bus']));
        $this->assertCount(1, $result); // page2
        $this->assertSame($page2->id, $result->first()->id);
    }

    public function test_orders_by_last_modified_desc(): void
    {
        $page1 = Page::factory()->create([
            'site_name' => SiteName::Japan,
            'last_modified' => now()->subDays(2),
        ]);
        $page1->paks()->attach($this->pak128);

        $page2 = Page::factory()->create([
            'site_name' => SiteName::Japan,
            'last_modified' => now(),
        ]);
        $page2->paks()->attach($this->pak128);

        $page3 = Page::factory()->create([
            'site_name' => SiteName::Japan,
            'last_modified' => now()->subDays(1),
        ]);
        $page3->paks()->attach($this->pak128);

        $searchAction = new SearchAction;

        $result = $searchAction([
            'keyword' => '',
            'sites' => [SiteName::Japan->value],
            'paks' => [PakSlug::Pak128->value],
        ]);

        $this->assertCount(3, $result);
        $this->assertSame($page2->id, $result->items()[0]->id);
        $this->assertSame($page3->id, $result->items()[1]->id);
        $this->assertSame($page1->id, $result->items()[2]->id);
    }
}

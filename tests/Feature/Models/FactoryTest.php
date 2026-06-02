<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Page;
use App\Models\Pak;
use App\Models\RawPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\TestCase;

final class FactoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_pak_factory_creates_record(): void
    {
        $pak = Pak::factory()->create();
        $this->assertDatabaseHas('paks', [
            'id' => $pak->id,
            'name' => $pak->name,
            'slug' => $pak->slug->value,
        ]);
    }

    public function test_raw_page_factory_creates_record(): void
    {
        $rawPage = RawPage::factory()->create();
        $this->assertDatabaseHas('raw_pages', [
            'id' => $rawPage->id,
            'site_name' => $rawPage->site_name->value,
            'url' => $rawPage->url,
        ]);
    }

    public function test_page_factory_creates_record(): void
    {
        $page = Page::factory()->create();
        $this->assertDatabaseHas('pages', [
            'id' => $page->id,
            'site_name' => $page->site_name->value,
            'url' => $page->url,
            'title' => $page->title,
        ]);
    }
}

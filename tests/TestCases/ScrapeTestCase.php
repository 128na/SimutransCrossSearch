<?php

namespace Tests\TestCases;

use App\Models\Page;
use App\Models\Pak;
use App\Models\RawPage;
use Mockery;
use Tests\TestCase;

abstract class ScrapeTestCase extends TestCase
{
    protected $site_service_class;

    protected function setUp(): void
    {
        parent::setUp();

        // スクレイピング処理をモックする
        $this->instance($this->site_service_class,
            Mockery::mock($this->site_service_class, [app(RawPage::class), app(Page::class), app(Pak::class)], function ($mock) {
                $mock->shouldReceive('getUrls')->andReturn(collect(['http://example.com']));
                $mock->shouldReceive('getHTML')->times(2)->andReturn('first example', 'second example');
            })->makePartial()
        );
    }
}

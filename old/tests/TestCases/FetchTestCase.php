<?php

namespace Tests\TestCases;

use App\Models\Article;
use Mockery;
use Tests\TestCase;

abstract class FetchTestCase extends TestCase
{
    protected $media_service_class;

    protected function setUp(): void
    {
        parent::setUp();

        // スクレイピング処理をモックする
        $this->instance($this->media_service_class,
            Mockery::mock($this->media_service_class, [app(Article::class)], function ($mock) {
                $mock->shouldReceive('search')->andReturn(collect([
                    [
                        'title' => 'test title',
                        'text' => 'test text',
                        'media_type' => 'video',
                        'url' => 'http://example.com',
                        'thumbnail_url' => 'http://example.com/thumb',
                        'last_modified' => now(),
                    ],
                ]));
            })->makePartial()
        );
    }
}

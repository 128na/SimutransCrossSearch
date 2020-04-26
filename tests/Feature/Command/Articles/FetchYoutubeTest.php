<?php

namespace Tests\Feature\Command\Articles;

use App\Services\MediaService\YoutubeMediaService as MediaService;
use Tests\TestCases\FetchTestCase;

class FetchYoutubeTest extends FetchTestCase
{
    protected $media_service_class = MediaService::class;

    public function testFetch()
    {
        $command = 'media:fetch youtube';

        $this->assertDatabaseMissing('articles', ['url' => 'http://example.com']);

        $this->artisan($command)->assertExitCode(0);
        $this->assertDatabaseHas('articles', [
            'site_name' => 'youtube',
            'url' => 'http://example.com',
            'title' => 'test title',
            'text' => 'test text',
            'media_type' => 'video',
            'thumbnail_url' => 'http://example.com/thumb',
        ]);
    }
}

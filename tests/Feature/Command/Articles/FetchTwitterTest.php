<?php

namespace Tests\Feature\Command\Articles;

use App\Services\MediaService\TwitterMediaService as MediaService;
use Tests\TestCases\FetchTestCase;

class FetchTwitterTest extends FetchTestCase
{
    protected $media_service_class = MediaService::class;

    public function testFetch()
    {
        $command = 'media:fetch twitter';

        $this->assertDatabaseMissing('articles', ['url' => 'http://example.com']);

        $this->artisan($command)->assertExitCode(0);
        $this->assertDatabaseHas('articles', [
            'site_name' => 'twitter',
            'url' => 'http://example.com',
            'title' => 'test title',
            'text' => 'test text',
            'media_type' => 'video',
            'thumbnail_url' => 'http://example.com/thumb',
        ]);
    }
}

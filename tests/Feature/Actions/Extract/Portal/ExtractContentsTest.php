<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Extract\Portal;

use App\Actions\Extract\Portal\ExtractContents;
use App\Actions\Extract\Portal\FindFileInfo;
use App\Enums\PakSlug;
use App\Enums\Portal\ArticlePostType;
use App\Models\Portal\Article;
use App\Models\Portal\Category;
use App\Models\Portal\FileInfo;
use App\Models\Portal\Tag;
use Mockery;
use Tests\Feature\TestCase;

final class ExtractContentsTest extends TestCase
{
    public function test_extracts_title_text_and_pak(): void
    {
        $article = new Article([
            'title' => 'Portal Addon Title',
            'post_type' => ArticlePostType::AddonPost,
            'contents' => [
                'description' => 'A nice addon.',
                'thanks' => 'Thanks to the author.',
                'license' => 'MIT',
                'file' => 123,
            ],
        ]);

        $tag = new Tag(['name' => 'Train', 'description' => 'A train addon']);
        $article->setRelation('tags', collect([$tag]));

        $category = new Category(['slug' => '128-japan']);
        $article->setRelation('categories', collect([$category]));

        $mockFindFileInfo = Mockery::mock(FindFileInfo::class);
        $mockFindFileInfo->shouldReceive('__invoke')
            ->with(123)
            ->andReturn(new FileInfo(['data' => 'file content info']));

        $action = new ExtractContents($mockFindFileInfo);
        $result = $action($article);

        $this->assertSame('Portal Addon Title', $result['title']);
        $this->assertStringContainsString('A nice addon.', $result['text']);
        $this->assertStringContainsString('Thanks to the author.', $result['text']);
        $this->assertStringContainsString('MIT', $result['text']);
        $this->assertStringContainsString('Train', $result['text']);
        $this->assertStringContainsString('A train addon', $result['text']);
        $this->assertStringContainsString('file content info', $result['text']);

        $this->assertEquals([PakSlug::Pak128Jp], $result['paks']);
    }
}

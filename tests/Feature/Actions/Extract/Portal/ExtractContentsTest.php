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
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\Feature\TestCase;

final class ExtractContentsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['database.connections.portal' => [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]]);
        Schema::connection('portal')->create('file_infos', function (Blueprint $blueprint): void {
            $blueprint->id();
            $blueprint->integer('attachment_id');
            $blueprint->text('data')->nullable();
            $blueprint->timestamps();
        });
    }

    public function test_extracts_title_text_and_pak(): void
    {
        $fileInfo = new FileInfo;
        $fileInfo->attachment_id = 123;
        $fileInfo->data = 'file content info';
        $fileInfo->save();

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

        $extractContents = new ExtractContents(new FindFileInfo);
        $result = $extractContents($article);

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

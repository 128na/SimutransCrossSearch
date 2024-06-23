<?php

declare(strict_types=1);

namespace App\Actions\Extract\Portal;

use App\Enums\PakSlug;
use App\Enums\Portal\ArticlePostType;
use App\Models\Portal\Article;

final readonly class ExtractContents
{
    public function __construct(
        private FindFileInfo $findFileInfo,
    ) {}

    /**
     * @return array{title:string,text:string,paks:PakSlug[]}
     */
    public function __invoke(Article $article): array
    {
        $title = $article->title;
        $text = $this->extractText($article);
        $pakSlugs = $this->extractPaks($article);

        return [
            'title' => $title,
            'text' => $text,
            'paks' => $pakSlugs,
        ];
    }

    private function extractText(Article $article): string
    {
        $fields = [];
        $fields[] = $article->contents['description'] ?? '';
        $fields[] = $article->contents['thanks'] ?? '';
        $fields[] = $article->contents['license'] ?? '';
        $fields[] = $article->tags->pluck('name')->implode("\n");
        $fields[] = $article->tags->pluck('description')->implode("\n");

        if ($article->post_type === ArticlePostType::AddonPost) {
            $fileInfo = ($this->findFileInfo)((int) $article->contents['file']);
            if ($fileInfo instanceof \App\Models\Portal\FileInfo) {
                $fields[] = $fileInfo->data ?? '';
            }
        }

        return implode("\n", $fields);
    }

    /**
     * @see https://github.com/128na/simutrans-portal/blob/master/database/seeders/CategorySeeder.php
     *
     * @return array<PakSlug>
     */
    private function extractPaks(Article $article): array
    {
        $result = [];
        foreach ($article->categories as $category) {
            if ($category->slug === '64') {
                $result[] = PakSlug::Pak64;
            }

            if ($category->slug === '128') {
                $result[] = PakSlug::Pak128;
            }

            if ($category->slug === '128-japan') {
                $result[] = PakSlug::Pak128Jp;
            }

        }

        return $result;
    }
}

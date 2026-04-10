<?php

declare(strict_types=1);

namespace App\Actions\Extract\Portal;

use App\Enums\PakSlug;
use App\Enums\Portal\ArticlePostType;
use App\Models\Portal\Article;
use App\Models\Portal\FileInfo;

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
        /** @var array<string, mixed> $contents */
        $contents = $article->contents ?? [];

        $fields = [];
        $fields[] = isset($contents['description']) && is_string($contents['description']) ? $contents['description'] : '';
        $fields[] = isset($contents['thanks']) && is_string($contents['thanks']) ? $contents['thanks'] : '';
        $fields[] = isset($contents['license']) && is_string($contents['license']) ? $contents['license'] : '';
        $fields[] = (string) $article->tags->pluck('name')->implode("\n");
        $fields[] = (string) $article->tags->pluck('description')->implode("\n");

        if ($article->post_type === ArticlePostType::AddonPost) {
            $fileId = $contents['file'] ?? null;
            if (is_numeric($fileId)) {
                $fileInfo = ($this->findFileInfo)(intval($fileId));
                if ($fileInfo instanceof FileInfo) {
                    $fields[] = is_string($fileInfo->data ?? null) ? $fileInfo->data : '';
                }
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

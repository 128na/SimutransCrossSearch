<?php

declare(strict_types=1);

namespace App\Actions\Extract\JapanWiki;

use App\Actions\Extract\FailedExtractExpection;
use App\Constants\JapanWikiPaks;
use App\Enums\PakSlug;
use App\Models\RawPage;
use Symfony\Component\DomCrawler\Crawler;

class ExtractContents
{
    public function __construct(
        private readonly Crawler $crawler
    ) {
    }

    /**
     * @return array{title:string,text:string,paks:PakSlug[]}
     */
    public function extractContents(RawPage $rawPage): array
    {
        $this->crawler->addHtmlContent($rawPage->html, 'UTF-8');

        $title = $this->extractTitle();
        $text = $this->extractText();
        $pakSlug = $this->extractPaks($rawPage->url);

        return [
            'title' => $title,
            'text' => $text,
            'paks' => [$pakSlug],
        ];
    }

    private function extractTitle(): string
    {
        $title = $this->crawler->filter('title')->text();

        return str_replace(' - Simutrans日本語化･解説', '', $title);
    }

    private function extractText(): string
    {
        return $this->crawler->filter('div#body')->text();
    }

    private function extractPaks(string $url): PakSlug
    {
        if (str_contains($url, JapanWikiPaks::PAK)) {
            return PakSlug::Pak64;
        }

        if (str_contains($url, JapanWikiPaks::PAK128)) {
            return PakSlug::Pak128;
        }

        if (str_contains($url, JapanWikiPaks::PAK128JP)) {
            return PakSlug::Pak128Jp;
        }

        throw new FailedExtractExpection('extractPaks failed');
    }
}

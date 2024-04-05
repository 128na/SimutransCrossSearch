<?php

declare(strict_types=1);

namespace App\Actions\Extract\Twitrans;

use App\Actions\Extract\FailedExtractExpection;
use App\Enums\PakSlug;
use App\Models\RawPage;
use Symfony\Component\DomCrawler\Crawler;

class ExtractContents
{
    /**
     * @return array{title:string,text:string,paks:PakSlug[]}
     */
    public function __invoke(RawPage $rawPage): array
    {
        $crawler = $rawPage->getCrawler();

        $title = $this->extractTitle($crawler);
        $text = $this->extractText($crawler);
        $pakSlug = $this->extractPak($rawPage->url);

        return [
            'title' => $title,
            'text' => $text,
            'paks' => [$pakSlug],
        ];
    }

    private function extractTitle(Crawler $crawler): string
    {
        $title = $crawler->filter('title')->text();

        return str_replace(' - Simutrans的な実験室 Wiki*', '', $title);
    }

    private function extractText(Crawler $crawler): string
    {
        return $crawler->filter('div#content')->text();
    }

    private function extractPak(string $url): PakSlug
    {
        $url = strtolower($url);
        if (str_contains($url, 'pak64/')) {
            return PakSlug::Pak64;
        }

        if (str_contains($url, 'pak128/')) {
            return PakSlug::Pak128;
        }

        if (str_contains($url, 'pak128.japan/')) {
            return PakSlug::Pak128Jp;
        }

        throw new FailedExtractExpection('extractPaks failed');
    }
}

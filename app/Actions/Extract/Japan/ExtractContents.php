<?php

declare(strict_types=1);

namespace App\Actions\Extract\Japan;

use App\Actions\Extract\FailedExtractExpection;
use App\Enums\PakSlug;
use App\Models\RawPage;
use Symfony\Component\DomCrawler\Crawler;

final class ExtractContents
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

        return str_replace(' - Simutrans日本語化･解説', '', $title);
    }

    private function extractText(Crawler $crawler): string
    {
        return $crawler->filter('div#body')->text();
    }

    private function extractPak(string $url): PakSlug
    {
        $url = strtolower($url);
        if (str_contains($url, '%a5%a2%a5%c9%a5%aa%a5%f3%2f')) {  // アドオン/
            return PakSlug::Pak64;
        }

        if (str_contains($url, 'addons%2f64%2f')) {  // Addons/64/
            return PakSlug::Pak64;
        }

        if (str_contains($url, 'addon128%2f')) { // Addon128/
            return PakSlug::Pak128;
        }

        if (str_contains($url, 'addons%2f128%2f')) {  // Addons/128/
            return PakSlug::Pak128;
        }

        if (str_contains($url, 'addon128japan%2f')) {   // Addon128/Japan/
            return PakSlug::Pak128Jp;
        }

        throw new FailedExtractExpection('extractPaks failed');
    }
}

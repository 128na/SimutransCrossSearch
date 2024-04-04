<?php

declare(strict_types=1);

namespace App\Actions\Extract\Japan;

use App\Actions\Extract\FailedExtractExpection;
use Carbon\CarbonImmutable;
use Symfony\Component\DomCrawler\Crawler;

class ExtractLastModified
{
    public function __invoke(Crawler $crawler): CarbonImmutable
    {
        $el = $crawler->filter('div#lastmodified');
        if ($el->count() !== 0) {
            $text = $el->text();
            $text = str_replace('Last-modified:', '', $text);
            $text = trim($text);
            $text = str_replace([' (月)', ' (火)', ' (水)', ' (木)', ' (金)', ' (土)', ' (日)'], '', $text);

            $date = CarbonImmutable::createFromFormat('Y-m-d H:i:s', $text);
            if ($date instanceof CarbonImmutable) {
                return $date;
            }
        }

        logger('lastmodified element not found', [$crawler->html()]);
        throw new FailedExtractExpection('ExtractLastModified failed');
    }
}

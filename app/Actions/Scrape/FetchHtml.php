<?php

declare(strict_types=1);

namespace App\Actions\Scrape;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class FetchHtml
{
    public function __construct(
        private readonly int $retryTimes = 3,
        private readonly int $sleepMilliseconds = 100,
        private readonly bool $useCache = true,
        private readonly int $lifetimeSeconds = 3600,
    ) {
    }

    public function __invoke(string $url, ?string $fromEncoding = null): Crawler
    {
        $key = 'url:'.$url;
        if ($this->useCache && Cache::has($key)) {
            /** @var string */
            $html = Cache::get($key);
        } else {
            $result = retry($this->retryTimes, fn () => Http::get($url), $this->sleepMilliseconds);
            $html = mb_convert_encoding((string) $result->body(), 'UTF-8', $fromEncoding);
            Cache::put($key, $html, $this->lifetimeSeconds);
        }

        $crawler = app(Crawler::class);

        $crawler->addHtmlContent($html, 'UTF-8');

        return $crawler;
    }
}

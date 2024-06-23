<?php

declare(strict_types=1);

namespace App\Actions\Scrape;

use App\Enums\Encoding;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

final readonly class FetchHtml
{
    public function __construct(
        private int $retryTimes = 3,
        private int $sleepMilliseconds = 100,
        private bool $useCache = false,
        private int $lifetimeSeconds = 3600,
    ) {}

    public function __invoke(string $url, Encoding $fromEncoding): Crawler
    {
        $key = 'url:'.$url;
        if ($this->useCache && Cache::has($key)) {
            /** @var string */
            $html = Cache::get($key);
        } else {
            $result = retry($this->retryTimes, fn () => Http::get($url), $this->sleepMilliseconds);
            $html = $fromEncoding === Encoding::UTF_8
                ? $result->body()
                : mb_convert_encoding((string) $result->body(), Encoding::UTF_8->value, $fromEncoding->value);
            Cache::put($key, $html, $this->lifetimeSeconds);
        }

        $crawler = app(Crawler::class);

        $crawler->addHtmlContent($html, Encoding::UTF_8->value);

        return $crawler;
    }
}

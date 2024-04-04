<?php

declare(strict_types=1);

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\BrowserKit\Response;

class FetchHtml extends AbstractBrowser
{
    public function __construct(
        private int $retryTimes = 3,
        private int $sleepMilliseconds = 100,
        private bool $useCache = true,
        private int $lifetimeSeconds = 3600,
    ) {
        parent::__construct();
    }

    /**
     * @param  \Symfony\Component\BrowserKit\Request  $request
     */
    protected function doRequest($request): Response
    {
        return new Response($this->fetch($request->getUri()));
    }

    public function setRetryTimes(int $retryTimes): void
    {
        $this->retryTimes = $retryTimes;
    }

    public function setSleepMilliseconds(int $sleepMilliseconds): void
    {
        $this->sleepMilliseconds = $sleepMilliseconds;
    }

    public function setUseCache(bool $useCache): void
    {
        $this->useCache = $useCache;
    }

    public function setLifetimeSeconds(int $lifetimeSeconds): void
    {
        $this->lifetimeSeconds = $lifetimeSeconds;
    }

    private function fetch(string $url): string
    {
        $key = 'html::'.$url;
        if ($this->useCache && Cache::has($key)) {
            /** @var string */
            $body = Cache::get($key);
        } else {
            $response = Http::retry($this->retryTimes, $this->sleepMilliseconds)->get($url);
            if ($status = $response->status() !== 200) {
                throw new Exception(sprintf('%s returns status: %s', $url, $status), 1);
            }

            $body = $response->body();
            if ($this->useCache) {
                Cache::put($key, $body, $this->lifetimeSeconds);
            }

            usleep($this->sleepMilliseconds * 1000);
        }

        return $body;
    }
}

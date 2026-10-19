<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Session\Store;
use Illuminate\Support\Facades\Cache;

final class SerializationSafetyTest extends TestCase
{
    public function test_file_cache_preserves_plain_data_without_waking_cached_objects(): void
    {
        $repository = Cache::store('file');
        $key = 'serialization-safety-'.bin2hex(random_bytes(12));
        CacheWakeupProbe::$wokeUp = false;
        try {
            $repository->put($key, ['last_crawl' => '2026-10-19 12:00:00'], 60);
            $this->assertSame(['last_crawl' => '2026-10-19 12:00:00'], $repository->get($key));
            $repository->put($key, new CacheWakeupProbe, 60);
            $this->assertNotInstanceOf(CacheWakeupProbe::class, $repository->get($key));
            $this->assertFalse(CacheWakeupProbe::$wokeUp);
        } finally {
            $repository->forget($key);
        }
    }

    public function test_session_saves_json_and_restores_plain_search_state(): void
    {
        $session = app('session')->driver();
        $session->start();
        $session->put('search', ['keyword' => '鉄道', 'page' => 2]);
        $session->save();

        $payload = json_decode($session->getHandler()->read($session->getId()), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame(['keyword' => '鉄道', 'page' => 2], $payload['search']);
        $store = new Store('restored', $session->getHandler(), $session->getId(), 'json');
        $store->start();
        $this->assertSame(['keyword' => '鉄道', 'page' => 2], $store->get('search'));
    }
}

final class CacheWakeupProbe
{
    public static bool $wokeUp = false;

    public function __wakeup(): void
    {
        self::$wokeUp = true;
    }
}

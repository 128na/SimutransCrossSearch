<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Session\Store;
use Illuminate\Support\Facades\Cache;

final class SerializationSafetyTest extends TestCase
{
    public function test_file_cache_preserves_plain_data_without_waking_cached_objects(): void
    {
        $cache = Cache::store('file');
        $key = 'serialization-safety-'.bin2hex(random_bytes(12));
        CacheWakeupProbe::$wokeUp = false;
        try {
            $cache->put($key, ['last_crawl' => '2026-10-19 12:00:00'], 60);
            $this->assertSame(['last_crawl' => '2026-10-19 12:00:00'], $cache->get($key));
            $cache->put($key, new CacheWakeupProbe, 60);
            $this->assertNotInstanceOf(CacheWakeupProbe::class, $cache->get($key));
            $this->assertFalse(CacheWakeupProbe::$wokeUp);
        } finally {
            $cache->forget($key);
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
        $restored = new Store('restored', $session->getHandler(), $session->getId(), 'json');
        $restored->start();
        $this->assertSame(['keyword' => '鉄道', 'page' => 2], $restored->get('search'));
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

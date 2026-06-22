<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Tests\Feature\TestCase;

/**
 * C2: 本番モード（APP_DEBUG=false）で API 例外応答にスタックトレースや
 * 例外メッセージが含まれないこと。
 */
final class ApiErrorResponseTest extends TestCase
{
    public function test_exception_response_does_not_leak_trace_in_production_mode(): void
    {
        Config::set('app.debug', false);

        Route::get('/__test/throw', function (): never {
            throw new \RuntimeException('super-secret-detail-xyz');
        });

        $testResponse = $this->getJson('/__test/throw');

        $testResponse->assertStatus(500);
        $testResponse->assertJsonMissingPath('trace');
        $testResponse->assertJsonMissingPath('exception');
        // 汎用メッセージのみで、例外の中身が露出しないこと。
        $this->assertSame('Server Error', $testResponse->json('message'));
        $this->assertStringNotContainsString('super-secret-detail-xyz', $testResponse->getContent() ?: '');
    }
}

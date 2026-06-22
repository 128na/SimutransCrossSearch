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

    /**
     * 例外メッセージに実際の機密値（DB 接続情報）が含まれているケースでも
     * 本番モードでは API 応答に出ないこと。
     */
    public function test_api_response_does_not_leak_secret_embedded_in_exception_message(): void
    {
        Config::set('app.debug', false);
        Config::set('database.connections.mysql.password', 'super-db-password');

        Route::get('/__test/throw-with-secret', function (): never {
            $password = Config::string('database.connections.mysql.password');
            throw new \RuntimeException('connection refused for password='.$password);
        });

        $testResponse = $this->getJson('/__test/throw-with-secret');

        $testResponse->assertStatus(500);
        $testResponse->assertJsonMissingPath('trace');
        $testResponse->assertJsonMissingPath('exception');
        $this->assertSame('Server Error', $testResponse->json('message'));
        $this->assertStringNotContainsString('super-db-password', $testResponse->getContent() ?: '');
    }
}

<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Tests\Feature\TestCase;

/**
 * C1: デバッグページ（Web/HTML 経路）が本番モードで config やスタックトレースを露出しないこと。
 */
final class WebErrorResponseTest extends TestCase
{
    public function test_web_response_does_not_leak_trace_in_production_mode(): void
    {
        Config::set('app.debug', false);

        Route::get('/__test/throw-web', function (): never {
            throw new \RuntimeException('super-secret-detail-xyz');
        });

        $testResponse = $this->get('/__test/throw-web');

        $testResponse->assertStatus(500);

        $content = $testResponse->getContent() ?: '';

        $this->assertStringNotContainsString('super-secret-detail-xyz', $content);
        // Whoops/デバッグページ特有のマーカーが出ていないこと（trace の代わりに汎用エラーページが返る）。
        $this->assertStringNotContainsString('Stack trace', $content);
    }
}

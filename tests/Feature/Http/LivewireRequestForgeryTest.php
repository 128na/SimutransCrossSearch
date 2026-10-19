<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use App\Enums\PakSlug;
use App\Enums\SiteName;
use App\Models\Page;
use App\Models\Pak;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Feature\TestCase;

final class LivewireRequestForgeryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Exercise the real middleware instead of Laravel's automatic test bypass.
        $this->app->instance('env', 'production');
    }

    /** @param array<string, string> $headers */
    #[DataProvider('validRequestHeaders')]
    public function test_search_update_accepts_valid_session_token(array $headers): void
    {
        $pak = Pak::factory()->create(['slug' => PakSlug::Pak128]);
        $matching = Page::factory()->create(['title' => 'Steam Locomotive', 'site_name' => SiteName::Japan]);
        $matching->paks()->attach($pak);
        $other = Page::factory()->create(['title' => 'City Bus', 'site_name' => SiteName::Japan]);
        $other->paks()->attach($pak);

        $payload = $this->searchUpdatePayload();
        $payload['_token'] = session()->token();

        $response = $this->postJson(Livewire::getUpdateUri(), $payload, ['X-Livewire' => 'true', ...$headers]);

        $response->assertOk();
        $html = $response->json('components.0.effects.html');
        $this->assertIsString($html);
        $this->assertStringContainsString('Steam Locomotive', $html);
        $this->assertStringNotContainsString('City Bus', $html);
    }

    /** @return array<string, array{array<string, string>}> */
    public static function validRequestHeaders(): array
    {
        return [
            'modern browser' => [['Sec-Fetch-Site' => 'same-origin']],
            'legacy browser token fallback' => [[]],
        ];
    }

    #[DataProvider('invalidTokens')]
    public function test_search_update_rejects_missing_or_invalid_token_without_same_origin_header(?string $token): void
    {
        $payload = $this->searchUpdatePayload();
        if ($token !== null) {
            $payload['_token'] = $token;
        }

        $this->postJson(Livewire::getUpdateUri(), $payload, ['X-Livewire' => 'true'])
            ->assertStatus(419);
    }

    /** @return array<string, array{string|null}> */
    public static function invalidTokens(): array
    {
        return [
            'missing' => [null],
            'invalid' => ['not-the-session-token'],
        ];
    }

    /** @return array<string, mixed> */
    private function searchUpdatePayload(): array
    {
        $response = $this->get('/')->assertOk();
        $document = new DOMDocument;
        @$document->loadHTML($response->getContent());
        $nodes = (new DOMXPath($document))->query('//*[@*[name()="wire:snapshot"]]');
        $this->assertNotFalse($nodes);
        $component = $nodes->item(0);
        $this->assertInstanceOf(DOMElement::class, $component);
        $snapshot = $component->getAttribute('wire:snapshot');
        $this->assertIsString($snapshot);
        $this->assertNotSame('', $snapshot);

        return [
            'components' => [[
                'snapshot' => $snapshot,
                'updates' => ['keyword' => 'Locomotive'],
                'calls' => [['path' => '', 'method' => 'onConditionUpdate', 'params' => []]],
            ]],
        ];
    }
}

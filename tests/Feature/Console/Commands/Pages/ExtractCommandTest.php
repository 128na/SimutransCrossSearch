<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Commands\Pages;

use Tests\Feature\TestCase;

final class ExtractCommandTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $testResponse = $this->get('/');

        $testResponse->assertStatus(200);
    }
}

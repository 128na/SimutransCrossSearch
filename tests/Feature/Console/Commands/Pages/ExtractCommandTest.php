<?php

namespace Tests\Feature\Console\Commands\Pages;

use Tests\TestCase;

class ExtractCommandTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}

<?php

namespace Tests\Feature;

use Tests\TestCase;

class ShowTest extends TestCase
{
    public function testLogSearch()
    {
        $url = route('logs.search');
        $res = $this->get($url);
        $res->assertStatus(200);
    }
}

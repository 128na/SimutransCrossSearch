<?php

namespace Tests\Feature\Api\v1;

use Tests\TestCase;

class ShowTest extends TestCase
{
    public function testSearch()
    {
        $url = route('api.v1.search');
        $res = $this->get($url);
        $res->assertStatus(200);
    }
}

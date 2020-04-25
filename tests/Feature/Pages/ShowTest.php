<?php

namespace Tests\Feature\Pages;

use Tests\TestCase;

class ShowTest extends TestCase
{
    public function testIndex()
    {
        $url = route('pages.index');
        $res = $this->get($url);
        $res->assertStatus(200);
    }

    public function testSearch()
    {
        $url = route('pages.search');
        $res = $this->get($url);
        $res->assertStatus(200);
    }
}

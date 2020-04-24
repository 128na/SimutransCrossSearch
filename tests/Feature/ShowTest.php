<?php

namespace Tests\Feature;

use Tests\TestCase;

class ShowTest extends TestCase
{
    public function testIndex()
    {
        $url = route('index');
        $res = $this->get($url);
        $res->assertStatus(200);
    }

    public function testSearch()
    {
        $url = route('search');
        $res = $this->get($url);
        $res->assertStatus(200);
    }

    public function testSitemap()
    {
        $url = route('sitemap');
        $res = $this->get($url);
        $res->assertStatus(200);
    }

    public function testLogSchedule()
    {
        $url = route('logs.schedule');
        $res = $this->get($url);
        $res->assertStatus(200);
    }

    public function testLogSearch()
    {
        $url = route('logs.search');
        $res = $this->get($url);
        $res->assertStatus(200);
    }
}

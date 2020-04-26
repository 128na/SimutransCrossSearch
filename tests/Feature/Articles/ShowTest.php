<?php

namespace Tests\Feature\Articles;

use Tests\TestCase;

class ShowTest extends TestCase
{
    public function testIndex()
    {
        $url = route('articles.index');
        $res = $this->get($url);
        $res->assertStatus(200);
    }

    public function testValidate()
    {
        // media_types
        $url = route('articles.index', ['media_types' => 'invalid']);
        $res = $this->get($url);
        $res->assertSessionHasErrors(['media_types']);

        $url = route('articles.index', ['media_types' => ['invalid']]);
        $res = $this->get($url);
        $res->assertSessionHasErrors(['media_types.0']);
    }
}

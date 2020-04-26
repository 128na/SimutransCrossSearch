<?php

namespace Tests\Feature\Pages\Search;

use Tests\TestCase;

class ValidateTest extends TestCase
{
    public function testValidate()
    {
        $url = route('pages.search');
        $res = $this->get($url);
        $res->assertStatus(200);

        // word
        $url = route('pages.search', ['word' => str_repeat('a', 101)]);
        $res = $this->get($url);
        $res->assertSessionHasErrors(['word']);
        $url = route('pages.search', ['word' => ['array']]);
        $res = $this->get($url);
        $res->assertSessionHasErrors(['word']);

        // type
        $url = route('pages.search', ['type' => 'invalid']);
        $res = $this->get($url);
        $res->assertSessionHasErrors(['type']);

        // paks
        $url = route('pages.search', ['paks' => 'not array']);
        $res = $this->get($url);
        $res->assertSessionHasErrors(['paks']);
        $url = route('pages.search', ['paks' => ['invalid']]);
        $res = $this->get($url);
        $res->assertSessionHasErrors(['paks.0']);
    }
}

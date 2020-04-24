<?php

namespace Tests\Feature\Search;

use Tests\TestCase;

class ValidateTest extends TestCase
{
    public function testValidate()
    {
        $url = route('search');
        $res = $this->get($url);
        $res->assertStatus(200);

        // word
        $url = route('search', ['word' => str_repeat('a', 101)]);
        $res = $this->get($url);
        $res->assertSessionHasErrors(['word']);
        $url = route('search', ['word' => ['array']]);
        $res = $this->get($url);
        $res->assertSessionHasErrors(['word']);

        // type
        $url = route('search', ['type' => 'invalid']);
        $res = $this->get($url);
        $res->assertSessionHasErrors(['type']);

        // paks
        $url = route('search', ['paks' => 'not array']);
        $res = $this->get($url);
        $res->assertSessionHasErrors(['paks']);
        $url = route('search', ['paks' => ['invalid']]);
        $res = $this->get($url);
        $res->assertSessionHasErrors(['paks.0']);
    }
}

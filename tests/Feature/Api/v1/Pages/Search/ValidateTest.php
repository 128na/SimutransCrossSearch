<?php

namespace Tests\Feature\Api\v1\Pages\Search;

use Tests\TestCase;

class ValidateTest extends TestCase
{
    public function testValidate()
    {
        $url = route('api.v1.search');
        $res = $this->get($url);
        $res->assertStatus(200);

        // word
        $url = route('api.v1.search', ['word' => str_repeat('a', 101)]);
        $res = $this->get($url);
        $res->assertSessionHasErrors(['word']);
        $url = route('api.v1.search', ['word' => ['array']]);
        $res = $this->get($url);
        $res->assertSessionHasErrors(['word']);

        // type
        $url = route('api.v1.search', ['type' => 'invalid']);
        $res = $this->get($url);
        $res->assertSessionHasErrors(['type']);

        // paks
        $url = route('api.v1.search', ['paks' => 'not array']);
        $res = $this->get($url);
        $res->assertSessionHasErrors(['paks']);
        $url = route('api.v1.search', ['paks' => ['invalid']]);
        $res = $this->get($url);
        $res->assertSessionHasErrors(['paks.0']);
    }
}

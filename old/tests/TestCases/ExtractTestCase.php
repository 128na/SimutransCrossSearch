<?php

namespace Tests\TestCases;

use App\Models\Pak;
use Tests\TestCase;

abstract class ExtractTestCase extends TestCase
{
    protected $site_service_class;

    protected $pak64;

    protected $pak128;

    protected $pak128jp;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pak64 = Pak::where('slug', '64')->first();
        $this->pak128 = Pak::where('slug', '128')->first();
        $this->pak128jp = Pak::where('slug', '128-japan')->first();
    }
}

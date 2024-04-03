<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Pak;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProdSeeder extends Seeder
{
    use WithoutModelEvents;

    private const PAKS = [
        '64' => 'Pak.64',
        '128' => 'Pak.128',
        '128-japan' => 'Pak.128Japan',
    ];

    public function run(): void
    {
        foreach (self::PAKS as $slug => $name) {
            Pak::updateOrCreate(['slug' => $slug], ['name' => $name]);
        }
    }
}

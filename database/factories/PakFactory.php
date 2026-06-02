<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\PakSlug;
use App\Models\Pak;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pak>
 */
final class PakFactory extends Factory
{
    protected $model = Pak::class;

    public function definition(): array
    {
        return [
            'name' => 'Pak128',
            'slug' => PakSlug::Pak128,
        ];
    }
}

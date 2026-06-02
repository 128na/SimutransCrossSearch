<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\SiteName;
use App\Models\RawPage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RawPage>
 */
final class RawPageFactory extends Factory
{
    protected $model = RawPage::class;

    public function definition(): array
    {
        return [
            'site_name' => SiteName::Japan,
            'url' => $this->faker->url(),
            'html' => '<html><body>Example HTML</body></html>',
        ];
    }
}

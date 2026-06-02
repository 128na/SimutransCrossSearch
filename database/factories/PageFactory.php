<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\SiteName;
use App\Models\Page;
use App\Models\RawPage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Page>
 */
final class PageFactory extends Factory
{
    protected $model = Page::class;

    public function definition(): array
    {
        return [
            'raw_page_id' => RawPage::factory(),
            'site_name' => $this->faker->randomElement(SiteName::cases()),
            'url' => $this->faker->url(),
            'title' => $this->faker->sentence(),
            'text' => $this->faker->paragraph(),
            'last_modified' => now(),
        ];
    }
}

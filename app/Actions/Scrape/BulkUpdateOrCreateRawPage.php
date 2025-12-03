<?php

declare(strict_types=1);

namespace App\Actions\Scrape;

use App\Enums\SiteName;
use App\Models\RawPage;
use Illuminate\Support\Collection;

final class BulkUpdateOrCreateRawPage
{
    /**
     * @param  Collection<int,string>  $urls
     */
    public function __invoke(Collection $urls, SiteName $siteName, string $html): void
    {
        $now = now();
        $data = $urls->map(fn (string $url): array => [
            'url' => $url,
            'site_name' => $siteName->value,
            'html' => $html,
            'updated_at' => $now,
            'created_at' => $now,
        ])->all();

        RawPage::upsert(
            $data,
            ['url'],
            ['site_name', 'html', 'updated_at']
        );
    }
}

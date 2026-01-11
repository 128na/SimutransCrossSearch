<?php

declare(strict_types=1);

namespace App\Actions\Scrape;

use App\Enums\SiteName;
use App\Models\RawPage;
use App\Support\HtmlCompression;
use Illuminate\Support\Collection;

final class BulkUpdateOrCreateRawPage
{
    /**
     * @param  Collection<int,string>  $urls
     */
    public function __invoke(Collection $urls, SiteName $siteName, string $html): void
    {
        $now = now();
        // Ensure compression since Eloquent's upsert bypasses attribute casts.
        $compressed = HtmlCompression::encode($html);
        $data = $urls->map(fn (string $url): array => [
            'url' => $url,
            'site_name' => $siteName->value,
            'html' => $compressed,
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

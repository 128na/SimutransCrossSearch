<?php

declare(strict_types=1);

namespace App\Actions\Scrape;

use App\Enums\SiteName;
use App\Models\RawPage;

class UpdateOrCreateRawPage
{
    public function __invoke(string $url, SiteName $siteName, string $html): RawPage
    {
        return RawPage::updateOrCreate([
            'url' => $url,
        ], [
            'site_name' => $siteName,
            'html' => $html,
        ]);
    }
}

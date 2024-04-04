<?php

declare(strict_types=1);

namespace App\Actions\Extract;

use App\Enums\SiteName;
use App\Models\Page;
use Carbon\CarbonImmutable;

class UpdateOrCreatePage
{
    public function __invoke(int $rawPageId, string $url, SiteName $siteName, string $title, string $text, CarbonImmutable $lastModified): Page
    {
        return Page::updateOrCreate([
            'raw_page_id' => $rawPageId,
        ], [
            'url' => $url,
            'site_name' => $siteName,
            'title' => $title,
            'text' => $text,
            'last_modified' => $lastModified,
        ]);
    }
}

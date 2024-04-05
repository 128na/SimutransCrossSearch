<?php

declare(strict_types=1);

namespace App\Actions\Extract;

use App\Models\Page;
use App\Models\RawPage;
use Carbon\CarbonImmutable;

class UpdateOrCreatePage
{
    public function __invoke(RawPage $rawPage, string $title, string $text, CarbonImmutable $lastModified): Page
    {
        $page = $rawPage->page;
        if ($page) {
            $page->fill([
                'title' => $title,
                'text' => $text,
                'last_modified' => $lastModified,
            ])->save();
        } else {
            $page = $rawPage->page()->create([
                'url' => $rawPage->url,
                'site_name' => $rawPage->site_name,
                'title' => $title,
                'text' => $text,
                'last_modified' => $lastModified,
            ]);
        }

        return $page;
    }
}

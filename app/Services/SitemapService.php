<?php

namespace App\Services;

use App\Models\SearchLog;
use Illuminate\Support\Facades\Storage;
use Spatie\Sitemap\Sitemap as SitemapGenerator;
use Spatie\Sitemap\Tags\Url;

/**
 * sitemap
 * @see https://gitlab.com/Laravelium/Sitemap
 * @see https://www.sitemaps.org/protocol.html
 */
class SitemapService
{
    private const FILENAME = 'sitemap.xml';

    public function getOrGenerate()
    {
        return Storage::disk('public')->exists(self::FILENAME) ? $this->get() : $this->generate();
    }

    private function get()
    {
        return Storage::disk('public')->get(self::FILENAME);
    }

    public function generate()
    {
        $sitemap = SitemapGenerator::create();

        $add = function ($url, $priority, $change_frequency, $last_modification = null) use ($sitemap) {
            $last_modification = $last_modification ?? now();

            $sitemap->add(
                Url::create($url)
                    ->setPriority($priority)
                    ->setChangeFrequency($change_frequency)
                    ->setLastModificationDate($last_modification->toDate())
            );
        };

        // listing pages
        $add(route('pages.index'), '1', Url::CHANGE_FREQUENCY_DAILY);
        $add(route('articles.index'), '1', Url::CHANGE_FREQUENCY_DAILY);

        // logs
        foreach (SearchLog::orderBy('created_at', 'desc')->cursor() as $log) {
            $add(
                route('pages.search') . '?' . $log->query,
                '0.5',
                Url::CHANGE_FREQUENCY_DAILY,
                $log->updated_at
            );
        }
        $path = storage_path('app/public/' . self::FILENAME);
        $sitemap->writeToFile($path);

        return self::get();
    }

}

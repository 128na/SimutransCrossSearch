<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Encoding;
use App\Enums\SiteName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @property int $id
 * @property SiteName $site_name
 * @property string $url
 * @property string $html
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Page|null $page
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RawPage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RawPage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RawPage query()
 *
 * @mixin \Eloquent
 */
final class RawPage extends Model
{
    protected $fillable = [
        'site_name',
        'url',
        'html',
    ];

    protected $casts = [
        'site_name' => SiteName::class,
    ];

    private ?Crawler $crawler = null;

    /**
     * @return HasOne<Page,$this>
     */
    public function page(): HasOne
    {
        return $this->hasOne(Page::class);
    }

    public function getCrawler(): Crawler
    {
        if (! $this->crawler instanceof \Symfony\Component\DomCrawler\Crawler) {
            $this->crawler = app(Crawler::class);
            $this->crawler->addHtmlContent($this->stripScriptElement($this->html), Encoding::UTF_8->value);
        }

        return $this->crawler;
    }

    private function stripScriptElement(string $html): string
    {
        return preg_replace('/<script([\s\S]+?)script>/mi', '', $html) ?? '';
    }
}

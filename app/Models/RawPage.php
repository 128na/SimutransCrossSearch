<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Encoding;
use App\Enums\SiteName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Symfony\Component\DomCrawler\Crawler;

class RawPage extends Model
{
    use HasFactory;

    private ?Crawler $crawler = null;

    protected $fillable = [
        'site_name',
        'url',
        'html',
    ];

    protected $casts = [
        'site_name' => SiteName::class,
    ];

    /**
     * @return HasOne<Page>
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

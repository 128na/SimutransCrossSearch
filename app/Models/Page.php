<?php

namespace App\Models;

use App\Services\HTMLPurifyService;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = ['site_name', 'url', 'title', 'text', 'last_modified'];

    protected $dates = ['last_modified'];

    public function paks()
    {
        return $this->belongsToMany(Pak::class);
    }

    public function rawPage()
    {
        return $this->belongsTo(RawPage::class);
    }

    public function getDisplaySiteNameAttribute()
    {
        return config("sites.{$this->site_name}.display_name", '');
    }

    public function getSiteUrlAttribute()
    {
        return config("sites.{$this->site_name}.url", '');
    }

    public function linkWithWord($search_condition)
    {
        if (!$search_condition || !implode('', $search_condition['words'])) {
            return $this->url;
        }
        $word = implode(' ', $search_condition['words']);

        switch ($this->site_name) {
            case 'twitrans':
                return $this->linkWithWordForTwitrans($word);
            case 'japan':
                return $this->linkWithWordForJapan($word);
            case 'portal':
            default:
                return $this->url;
        }
    }
    private function linkWithWordForTwitrans($word)
    {
        return "{$this->url}?word={$word}";
    }
    private function linkWithWordForJapan($word)
    {
        $parsed = parse_url($this->url);
        $word = urlencode(mb_convert_encoding($word, 'EUC-JP', 'UTF-8'));
        $query = "cmd=read&page={$parsed['query']}&word={$word}";
        return "{$parsed['scheme']}://{$parsed['host']}{$parsed['path']}?{$query}";
    }

    public function highlightText($search_condition)
    {
        if (!implode('', $search_condition['words'])) {
            return '';
        }
        $word = implode('|', $search_condition['words']);

        $reg = "/(.{0,20}({$word}).{0,20})/iu";
        preg_match_all($reg, $this->text, $matches);

        $texts = collect($matches[0]);
        $texts->splice(10);

        $highlighted = $texts->map(function ($text) use ($word) {
            $reg = "/({$word})/iu";
            $rep = '<span class="highlight">$1</span>';
            return preg_replace($reg, $rep, $text);
        });

        $raw_html = '…' . $highlighted->implode('…, ') . '…';

        return app(HTMLPurifyService::class)->purifyHTML($raw_html);
    }
}

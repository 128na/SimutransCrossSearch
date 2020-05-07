<?php

namespace App\Models;

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

    public function highlightText($search_condition)
    {
        if (!implode('', $search_condition['words'])) {
            return '';
        }
        $word = implode('|', $search_condition['words']);

        $reg = "/(.{0,10}({$word}).{0,10})/iu";
        preg_match_all($reg, $this->text, $matches);

        $texts = collect($matches[0]);

        $highlighted = $texts->map(function ($text) use ($word) {
            $reg = "/({$word})/iu";
            $rep = '<span class="highlight">$1</span>';
            return preg_replace($reg, $rep, $text);
        });

        return $highlighted->implode('\n');
    }
}

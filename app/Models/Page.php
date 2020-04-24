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
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = ['site_name', 'url', 'title', 'media_type', 'text', 'thumbnail_url', 'last_modified'];

    protected $dates = ['last_modified'];

    public function getDisplaySiteNameAttribute()
    {
        return config("media.{$this->site_name}.display_name", '');
    }

    public function getDisplayMediaTypeAttribute()
    {
        $label = ['video' => '動画', 'image' => '画像'];
        return $label[$this->media_type];
    }

    public function getSiteUrlAttribute()
    {
        return config("media.{$this->site_name}.url", '');
    }
}

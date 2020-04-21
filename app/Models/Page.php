<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = ['site_name', 'url', 'title', 'text'];

    public function paks()
    {
        return $this->belongsToMany(Pak::class);
    }

    public function rawPage()
    {
        return $this->belongsTo(RawPage::class);
    }

}

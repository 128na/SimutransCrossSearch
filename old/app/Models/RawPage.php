<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RawPage extends Model
{
    protected $fillable = ['site_name', 'url', 'html'];

    public function page()
    {
        return $this->hasOne(Page::class);
    }
}

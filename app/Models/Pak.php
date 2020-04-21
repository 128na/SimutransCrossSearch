<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pak extends Model
{
    protected $fillable = ['name', 'slug'];

    public function pages()
    {
        return $this->hasMany(Page::class);
    }
}

<?php

namespace App\Models\Portal;

use App\Models\Article;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    protected $connection = 'portal';

    protected $fillable = [
        'name',
        'type',
        'slug',
        'order',
        'need_admin',
    ];

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class);
    }
}

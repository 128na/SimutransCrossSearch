<?php

namespace App\Models\Portal;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $connection = 'portal';

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'post_type',
        'contents',
        'status',
    ];

    protected $casts = [
        'contents' => 'json',
    ];

    /*
    |--------------------------------------------------------------------------
    | 初期化時設定
    |--------------------------------------------------------------------------
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('active', function (Builder $builder) {
            $builder->where('status', 'publish')
                ->whereIn('post_type', ['addon-post', 'addon-introduction']);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | リレーション
    |--------------------------------------------------------------------------
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function getTextContentsAttribute()
    {
        $contents = $this->contents['description'] ?? '';
        $contents .= $this->contents['thanks'] ?? '';
        $contents .= $this->contents['license'] ?? '';

        return $contents;
    }
}

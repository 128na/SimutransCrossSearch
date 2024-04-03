<?php

namespace App\Models\Portal;

use App\Enums\Portal\ArticlePostType;
use App\Enums\Portal\ArticleStatus;
use App\Models\Scopes\Portal\OnlyPublishAddon;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @see https://github.com/128na/simutrans-portal/blob/master/app/Models/Article.php
 */
#[ScopedBy([OnlyPublishAddon::class])]
class Article extends Model
{
    use HasFactory;

    protected $connection = 'portal';

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'post_type',
        'contents',
        'status',
        'published_at',
        'modified_at',
    ];

    protected $casts = [
        'contents' => 'json',
        'post_type' => ArticlePostType::class,
        'status' => ArticleStatus::class,
        'published_at' => 'immutable_datetime',
        'modified_at' => 'immutable_datetime',
    ];

    /**
     * @return BelongsToMany<Category>
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }
}

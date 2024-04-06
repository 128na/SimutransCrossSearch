<?php

declare(strict_types=1);

namespace App\Models\Portal;

use App\Enums\Portal\ArticlePostType;
use App\Enums\Portal\ArticleStatus;
use App\Models\Scopes\Portal\OnlyPublishAddon;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string $title タイトル
 * @property string $slug スラッグ
 * @property ArticlePostType $post_type 投稿形式
 * @property array $contents コンテンツ
 * @property ArticleStatus $status 公開状態
 * @property int $pr PR記事
 * @property \Carbon\CarbonImmutable|null $published_at 投稿日時
 * @property \Carbon\CarbonImmutable|null $modified_at 更新日時
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Portal\Category> $categories
 * @property-read int|null $categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Portal\Tag> $tags
 * @property-read int|null $tags_count
 * @method static \Illuminate\Database\Eloquent\Builder|Article newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Article newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Article query()
 * @mixin \Eloquent
 */
#[ScopedBy([OnlyPublishAddon::class])]
final class Article extends Model
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

    /**
     * @return BelongsToMany<Tag>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }
}

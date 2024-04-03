<?php

declare(strict_types=1);

namespace App\Models\Portal;

use App\Enums\Portal\CategoryType;
use App\Models\Scopes\Portal\OnlyPakCategory;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @see https://github.com/128na/simutrans-portal/blob/master/app/Models/Category.php
 */
#[ScopedBy(OnlyPakCategory::class)]
class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'slug',
    ];

    protected $casts = [
        'type' => CategoryType::class,
    ];

    /**
     * @return BelongsToMany<Article>
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Article::class);
    }
}

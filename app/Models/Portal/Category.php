<?php

declare(strict_types=1);

namespace App\Models\Portal;

use App\Enums\Portal\CategoryType;
use App\Models\Scopes\Portal\OnlyPakCategory;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property CategoryType $type 分類
 * @property string $slug スラッグ
 * @property int $need_admin 管理者専用カテゴリ
 * @property int $order 表示順
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category query()
 *
 * @mixin \Eloquent
 */
#[ScopedBy(OnlyPakCategory::class)]
final class Category extends Model
{
    protected $connection = 'portal';

    protected $fillable = [
        'type',
        'slug',
    ];

    protected $casts = [
        'type' => CategoryType::class,
    ];
}

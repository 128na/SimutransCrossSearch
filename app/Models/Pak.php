<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PakSlug;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property PakSlug $slug
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Page> $pages
 * @property-read int|null $pages_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pak newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pak newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pak query()
 *
 * @mixin \Eloquent
 */
final class Pak extends Model
{
    protected $fillable = [
        'name',
        'slug',
    ];

    protected $casts = [
        'slug' => PakSlug::class,
    ];

    /**
     * @return HasMany<Page,$this>
     */
    public function pages(): HasMany
    {
        return $this->hasMany(Page::class);
    }
}

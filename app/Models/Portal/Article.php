<?php

namespace App\Models\Portal;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('active', function (Builder $builder) {
            $builder->where('status', 'publish')
                ->whereIn('post_type', ['addon-post', 'addon-introduction']);
        });
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function getTextContentsAttribute(): string
    {
        $fields = [];
        $fields[] = $this->contents['description'] ?? '';
        $fields[] = $this->contents['thanks'] ?? '';
        $fields[] = $this->contents['license'] ?? '';
        $fields[] = $this->translatedCategory();
        $fields[] = $this->tags->pluck('name')->implode("\n");
        $fields[] = $this->tags->pluck('description')->implode("\n");

        return implode("\n", $fields);
    }

    private function translatedCategory(): string
    {
        return $this->categories
            ->map(fn (Category $c) => __("category.{$c->type}.{$c->slug}"))
            ->implode("\n");
    }
}

<?php

declare(strict_types=1);

namespace App\Models\Scopes\Portal;

use App\Enums\Portal\ArticlePostType;
use App\Enums\Portal\ArticleStatus;
use App\Models\Portal\Article;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/** @implements Scope<Article> */
final class OnlyPublishAddon implements Scope
{
    /**
     * @param  Builder<covariant Article>  $builder
     * @param  Article  $model
     */
    #[\Override]
    public function apply(Builder $builder, Model $model): void
    {
        $builder
            ->where('status', ArticleStatus::Publish)
            ->whereIn('post_type', [
                ArticlePostType::AddonIntroduction,
                ArticlePostType::AddonPost,
            ]);
    }
}

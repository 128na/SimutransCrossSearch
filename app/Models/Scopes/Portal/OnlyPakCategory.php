<?php

declare(strict_types=1);

namespace App\Models\Scopes\Portal;

use App\Enums\Portal\CategoryType;
use App\Models\Portal\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/** @implements Scope<Category> */
final class OnlyPakCategory implements Scope
{
    /**
     * @param  Builder<covariant Category>  $builder
     * @param  Category  $model
     */
    #[\Override]
    public function apply(Builder $builder, Model $model): void
    {
        $builder->where('type', CategoryType::Pak);
    }
}

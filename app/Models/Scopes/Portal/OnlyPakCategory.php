<?php

declare(strict_types=1);

namespace App\Models\Scopes\Portal;

use App\Enums\Portal\CategoryType;
use App\Models\Portal\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

final class OnlyPakCategory implements Scope
{
    /**
     * @param  Builder<Model>  $builder
     */
    #[\Override]
    public function apply(Builder $builder, Model $model): void
    {
        assert($model instanceof Category);

        $builder->where('type', CategoryType::Pak);
    }
}

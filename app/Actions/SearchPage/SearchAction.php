<?php

declare(strict_types=1);

namespace App\Actions\SearchPage;

use App\Models\Page;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class SearchAction
{
    /**
     * @param  array{keyword:string,paks:array<int,int|string>,sites:array<int,string>}  $data
     * @return LengthAwarePaginator<Page>
     */
    public function __invoke(array $data): LengthAwarePaginator
    {
        /**
         * @var LengthAwarePaginator<Page>
         */
        return Page::query()
            ->withWhereHas('paks', fn (Builder|BelongsToMany $builder) => $builder->whereIn('slug', $data['paks']))
            ->whereIn('site_name', $data['sites'])
            ->when($data['keyword'], fn (Builder $builder, $keyword) => $builder->where('text', 'like', sprintf('%%%s%%', $keyword)))
            ->orderBy('last_modified', 'desc')
            ->paginate(50)
            ->withQueryString();
    }
}

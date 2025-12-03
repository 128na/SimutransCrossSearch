<?php

declare(strict_types=1);

namespace App\Actions\SearchPage;

use App\Models\Page;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

final class SearchAction
{
    /**
     * @param  array{keyword:string,paks:array<int,int|string>,sites:array<int,string>,page?:int|numeric-string|null}  $data
     * @return LengthAwarePaginator<int,Page>
     */
    public function __invoke(array $data): LengthAwarePaginator
    {
        $query = Page::query()
            ->withWhereHas('paks', fn(Builder|Relation $builder) => $builder->whereIn('slug', $data['paks']))
            ->whereIn('site_name', $data['sites']);

        $this->addKeywordQuery($query, $data['keyword']);

        return $query->orderBy('last_modified', 'desc')
            ->paginate(perPage: 50, page: (int) ($data['page'] ?? 1))
            ->withQueryString();
    }

    /**
     * @param  Builder<Page>  $builder
     */
    private function addKeywordQuery(Builder $builder, string $keyword): void
    {
        $builder->where(function (Builder $q) use ($keyword): void {
            foreach (explode(' ', $keyword) as $word) {
                $word = trim($word);
                if (str_starts_with($word, '-')) {
                    $word = trim(substr($word, 1));
                    if ($word !== '' && $word !== '0') {
                        $q->where('title', 'not like', sprintf('%%%s%%', $word));
                        $q->orWhere('text', 'not like', sprintf('%%%s%%', $word));
                    }
                } else {
                    $q->where('title', 'like', sprintf('%%%s%%', $word));
                    $q->orWhere('text', 'like', sprintf('%%%s%%', $word));
                }
            }
        });
    }
}

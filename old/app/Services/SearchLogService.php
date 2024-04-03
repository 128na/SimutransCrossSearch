<?php

namespace App\Services;

use App\Models\SearchLog;
use Illuminate\Pagination\Paginator;

class SearchLogService
{
    private SearchLog $model;

    public function __construct(SearchLog $model)
    {
        $this->model = $model;
    }

    /**
     * 検索履歴の保存
     */
    public function put(string $query): SearchLog
    {
        $model = $this->model->firstOrNew(['query' => $query]);

        if ($model->isDirty()) {
            $model->save();
        } else {
            $model->update(['count' => $model->count + 1]);
        }

        return $model;
    }

    public function getRanking($limit = 20): Paginator
    {
        return $this->model
            ->orderBy('count', 'desc')
            ->simplePaginate($limit);
    }
}

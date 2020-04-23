<?php
namespace App\Services;

use App\Models\Page;
use App\Models\SearchLog;
use App\Models\SearchWords;

class PageSearchService
{
    /**
     * @var Page
     */
    private $page;
    /**
     * @var SearchLog
     */
    private $search_log;

    public function __construct(Page $page, SearchLog $search_log)
    {
        $this->page = $page;
        $this->search_log = $search_log;
    }

    public function latest($limit = 20)
    {
        return $this->page
            ->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->with('paks')
            ->get();
    }

    public function search(SearchWords $search_words, $paks, $per_page = 20)
    {

        $query = $this->buildWordSearchQuery(
            $this->page->query(),
            $search_words->getWords()
        );

        if (count($paks)) {
            $query = $this->buildPakQuery($query, $paks);
        }

        $res = $query
            ->orderBy('updated_at', 'desc')
            ->with('paks')
            ->paginate($per_page);

        return $res;
    }

    private function buildWordSearchQuery($query, $words)
    {
        $words->each(function ($word) use ($query) {
            $query->where(function ($query) use ($word) {
                $query->where('text', 'like', "%{$word}%")
                    ->orWhere('title', 'like', "%{$word}%");
            });
        });
        return $query;
    }

    private function buildPakQuery($query, $paks)
    {
        return $query->whereHas('paks', function ($query) use ($paks) {
            $query->whereIn('slug', $paks);
        });
    }

    public function getTitle($word, $paks)
    {
        $cond = collect([$word]);
        if (count($paks)) {
            $paks = collect($paks)->map(function ($slug) {
                return config("paks.{$slug}");
            });
            $cond = $cond->merge($paks);
        }
        $cond = $cond->implode(', ');
        return "「{$cond}」での検索結果";
    }

    /**
     * 検索履歴の保存
     */
    public function updateSearchLog(String $query): SearchLog
    {
        $log = $this->search_log->firstOrNew(['query' => $query]);

        if ($log->isDirty()) {
            $log->save();
        } else {
            $log->update(['count' => $log->count + 1]);
        }
        return $log;
    }

}

<?php
namespace App\Services;

use App\Models\Page;
use App\Models\SearchLog;

class SearchService
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

    public function search(string $word, string $type, array $paks, $per_page = 20)
    {
        $search_condition = $this->parseSearchCondition($word, $type);
        $query = $this->buildWordQuery($this->page->query(), $search_condition);

        if (count($paks)) {
            $query = $this->buildPakQuery($query, $paks);
        }

        $res = $query
            ->orderBy('updated_at', 'desc')
            ->with('paks')
            ->paginate($per_page);

        return $res;
    }

    private function buildWordQuery($query, $search_condition)
    {
        return $query->where(function ($query) use ($search_condition) {
            $where_method = $search_condition['type'] === 'and' ? 'where' : 'orWhere';
            foreach ($search_condition['words'] as $word) {
                $query->$where_method(function ($query) use ($word) {
                    $query->where('text', 'like', "%{$word}%")
                        ->orWhere('title', 'like', "%{$word}%");
                });
            }
        });
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

    public function parseSearchCondition($word, $type = 'and')
    {
        $word = $this->clean($word);
        return [
            'words' => explode(' ', $word),
            'type' => $type,
        ];
    }

    /**
     * 検索条件に関わる文字を統一
     */
    private function clean($str)
    {
        $from = ['　'];
        $to = ' ';
        $str = str_replace($from, $to, $str);
        $str = mb_strtolower($str);
        $str = trim($str);
        return $str;
    }

    /**
     * 検索履歴の保存
     */
    public function putSearchLog(String $query): SearchLog
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

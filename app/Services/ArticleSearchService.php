<?php
namespace App\Services;

use App\Models\Article;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ArticleSearchService
{
    /**
     * @var Article
     */
    private $model;

    public function __construct(Article $model)
    {
        $this->model = $model;
    }

    public function latest($limit = 20): Collection
    {
        return $this->model
            ->select('id', 'site_name', 'media_type', 'title', 'url', 'thumbnail_url', 'last_modified')
            ->orderBy('last_modified', 'desc')
            ->limit($limit)
            ->get();
    }

    public function search(string $word, string $type, array $paks, $per_page = 20): LengthAwarePaginator
    {
        $query = $this->model
            ->select('id', 'site_name', 'media_type', 'title', 'url', 'thumbnail_url', 'last_modified');

        $search_condition = $this->parseSearchCondition($word, $type);
        $query = $this->buildWordQuery($query, $search_condition);

        if (count($paks)) {
            $query = $this->buildPakQuery($query, $paks);
        }

        return $query
            ->orderBy('last_modified', 'desc')
            ->with('paks')
            ->paginate($per_page);
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
}

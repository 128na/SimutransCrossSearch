<?php

namespace App\Services\MediaService;

use App\Models\Article;
use Illuminate\Support\Collection;

abstract class MediaService
{
    /**
     * @var String
     */
    protected $name;

    /**
     * @var String
     */
    protected $url;

    /**
     * @var Article
     */
    protected $article;

    public function __construct(array $config, Article $article)
    {
        $this->name = $config['name'];
        $this->url = $config['url'];
        $this->article = $article;
    }

    abstract public function search(string $word, $limit = 50): Collection;

    public function saveArticleIfNeeded(array $data): ?Article
    {
        if ($this->article->where('url', $data['url'])->exists()) {
            return null;
        }
        return $this->article->create([
            'site_name' => $this->name,
            'url' => $data['url'],
            'title' => $data['title'],
            'text' => $data['text'],
            'media_type' => $data['media_type'],
            'thumbnail_url' => $data['thumbnail_url'],
            'last_modified' => $data['last_modified'],
        ]);
    }

    public function removeExcludes(Collection $urls): int
    {
        return $this->article
            ->where('site_name', $this->name)
            ->whereNotIn('url', $urls->toArray())
            ->delete();
    }
}

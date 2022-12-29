<?php

namespace App\Console\Commands\Articles;

use App\Factories\MediaServiceFactory;
use App\Services\MediaService\MediaService;
use Illuminate\Console\Command;

class FetchOldestCommand extends Command
{
    protected $signature = 'media:back {name}';

    protected $description = '取得済みの最古の記事からさかのぼって昔の記事を探す';

    private MediaServiceFactory $service_factory;

    private MediaService $media_service;

    public function __construct(MediaServiceFactory $service_factory)
    {
        parent::__construct();
        $this->service_factory = $service_factory;
    }

    public function handle()
    {
        $name = $this->argument('name');
        $this->media_service = $this->service_factory->make($name);

        $oldest = $this->media_service->getOldestArticle();
        $oldest_last_modified = $oldest->last_modified;
        $limit = 50;

        do {
            $items = $this->media_service->searchOld('Simutrans', $oldest_last_modified, $limit);
            $this->info('find.'.$items->count());
            if ($items->count()) {
                $articles = $items->map(function ($item) {
                    return $this->media_service->saveArticleIfNeeded($item);
                })->filter();
                $this->info('save.'.$articles->count());
                $oldest_last_modified = $items->last()['last_modified'];
                sleep(1);
            }
        } while ($items->count() >= $limit);

        return 0;
    }
}

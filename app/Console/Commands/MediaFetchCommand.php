<?php

namespace App\Console\Commands;

use App\Factories\MediaServiceFactory;
use App\Services\MediaService\MediaService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use \Throwable;

class MediaFetchCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:fetch {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * @var MediaServiceFactory
     */
    private $service_factory;
    /**
     * @var MediaService
     */
    private $media_service;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(MediaServiceFactory $service_factory)
    {
        parent::__construct();
        $this->service_factory = $service_factory;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        DB::beginTransaction();

        $name = $this->argument('name');
        $this->media_service = $this->service_factory->make($name);

        try {
            $items = $this->media_service->search('Simutrans', 50);

            $article_urls = $items->map(function ($item) {
                $article = $this->media_service->saveArticleIfNeeded($item);
                return $item['url'];
            });

            $this->media_service->removeExcludes($article_urls);

        } catch (Throwable $e) {
            logger()->error($e->getMessage());
            throw $e;
        }
        DB::commit();
        $this->info(sprintf('%d article updated', $items->count()));
    }
}

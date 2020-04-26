<?php

namespace App\Console\Commands;

use App\Factories\SiteServiceFactory;
use App\Services\SiteService\SiteService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use \Throwable;

class ScrapeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'page:scrape {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'scrape sites to raw pages';

    /**
     * @var SiteServiceFactory
     */
    private $service_factory;
    /**
     * @var SiteService
     */
    private $site_service;
    /**
     * @var null|Throwable
     */
    private $last_error = null;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(SiteServiceFactory $service_factory)
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
        $this->site_service = $this->service_factory->make($name);

        try {
            $urls = $this->site_service->getUrls();
        } catch (Throwable $e) {
            logger()->error($e->getMessage());
            throw $e;
        }

        $raw_page_urls = $urls->map(function ($url) {
            try {
                $this->info($url);
                $raw_page = retry(3, function () use ($url) {
                    $html = $this->site_service->getHTML($url);
                    return $this->site_service->saveOrUpdateRawPage($url, $html);
                }, 1000);
                return $raw_page->url;
            } catch (Throwable $e) {
                $this->last_error = $e;
                logger()->error($e->getMessage());
            }
        });

        DB::commit();
        if ($this->last_error) {
            throw $this->last_error;
        }
        $this->info(sprintf('%d raw page updated', $raw_page_urls->count()));

        DB::beginTransaction();
        $count = $this->site_service->removeExcludes($raw_page_urls);
        DB::commit();
        $this->info(sprintf('%d raw page deleted', $count));
    }
}

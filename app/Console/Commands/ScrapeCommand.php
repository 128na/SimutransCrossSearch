<?php

namespace App\Console\Commands;

use App\Factories\SiteServiceFactory;
use App\Services\SiteService\SiteService;
use Illuminate\Console\Command;

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
    private $site_service_factory;
    /**
     * @var SiteService
     */
    private $site_service;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(SiteServiceFactory $site_service_factory)
    {
        parent::__construct();
        $this->site_service_factory = $site_service_factory;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->argument('name');
        $this->site_service = $this->site_service_factory->make($name);

        $urls = $this->site_service->getUrls();

        $raw_page_urls = $urls->map(function ($url) {
            $this->info($url);
            $raw_page = retry(3, function () use ($url) {
                $html = $this->site_service->getHTML($url);
                return $this->site_service->saveOrUpdateRawPage($url, $html);
            }, 1000);
            return $raw_page->url;
        });

        $this->info(sprintf('%d raw page updated', $raw_page_urls->count()));

        $count = $this->site_service->removeExcludes($raw_page_urls);

        $this->info(sprintf('%d raw page deleted', $count));
    }
}

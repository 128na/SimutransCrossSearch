<?php

namespace App\Console\Commands;

use App\Factories\SiteServiceFactory;
use App\Models\RawPage;
use App\Services\SiteService\SiteService;
use Illuminate\Console\Command;

class ExtractCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'extract:page {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'extract page';

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

        $raw_pages = $this->site_service->getUpdatedRawPages();

        $result = $raw_pages->map(function (RawPage $raw_page) {
            $this->info($raw_page->id);

            $data = $this->site_service->extractContents($raw_page);
            return $this->site_service->saveOrUpdatePage($raw_page, $data);
        });

        $this->info(sprintf('%d page updated', $result->count()));
    }
}

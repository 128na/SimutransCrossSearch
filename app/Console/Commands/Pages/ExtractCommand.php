<?php

namespace App\Console\Commands\Pages;

use App\Factories\SiteServiceFactory;
use App\Models\RawPage;
use App\Services\SiteService\SiteService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use \Throwable;

class ExtractCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'page:extract {name} {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'extract from raw pages to pages contents';

    /**
     * @var SiteServiceFactory
     */
    private $site_service_factory;
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
        DB::beginTransaction();
        $name = $this->argument('name');
        $this->site_service = $this->site_service_factory->make($name);

        $raw_pages = $this->option('all')
        ? $this->site_service->getAllRawPages()
        : $this->site_service->getUpdatedRawPages();

        $result = $raw_pages->map(function (RawPage $raw_page) {
            try {
                $this->info($raw_page->id);

                $data = $this->site_service->extractContents($raw_page);
                return $this->site_service->saveOrUpdatePage($raw_page, $data);
            } catch (Throwable $e) {
                $this->last_error = $e;
                logger()->error($e->getMessage());
            }
        });

        DB::commit();
        if ($this->last_error) {
            throw $this->last_error;
        }
        $this->info(sprintf('%d page updated', $result->count()));

    }
}

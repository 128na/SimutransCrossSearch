<?php

namespace App\Factories;

use App\Services\SiteService\JapaneseSimutransSiteService;
use App\Services\SiteService\SimutransAddonPortalSiteService;
use App\Services\SiteService\SiteService;
use App\Services\SiteService\TwitransSiteService;
use Exception;

class SiteServiceFactory
{
    public function make($name): SiteService
    {
        switch ($name) {
            case 'portal':
                return app(SimutransAddonPortalSiteService::class);
            case 'japan':
                return app(JapaneseSimutransSiteService::class);
            case 'twitrans':
                return app(TwitransSiteService::class);
            default:
                throw new Exception("{$name} is not defined SiteService", 1);
        }
    }
}

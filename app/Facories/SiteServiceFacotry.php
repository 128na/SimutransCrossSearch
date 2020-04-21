<?php

namespace App\Facories;

use App\Services\SiteService\SimutransAddonPortalSiteService;
use App\Services\SiteService\SimutransJapanSiteService;
use App\Services\SiteService\TwitransSiteService;
use Exception;

class SiteServiceFactory
{
    public function make($name)
    {
        switch ($name) {
            case 'simutrans-addon-portal':
                return app(SimutransAddonPortalSiteService::class);
            case 'simutrans-japan':
                return app(SimutransJapanSiteService::class);
            case 'twitrans':
                return app(TwitransSiteService::class);
            default:
                throw new Exception("{$name} is not defined SiteService", 1);

        }
    }
}

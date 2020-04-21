<?php

namespace App\Services\SiteService;

use App\Models\Page;

class SimutransAddonPortalSiteService extends BaseSiteService
{
    public function __construct(Page $page)
    {
        parent::__construct(
            config('sites.simutrans-addon-portal'),
            $page
        );
    }

}

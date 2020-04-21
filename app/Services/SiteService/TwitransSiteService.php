<?php

namespace App\Services\SiteService;

use App\Models\Page;

class TwitransSiteService extends BaseSiteService
{
    public function __construct(Page $page)
    {
        parent::__construct(
            config('sites.twitrans'),
            $page
        );
    }
}

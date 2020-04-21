<?php

namespace App\Services\SiteService;

use App\Models\Page;

class JapaneseSimutransSiteService extends BaseSiteService
{
    public function __construct(Page $page)
    {
        parent::__construct(
            config('sites.simutrans-japan'),
            $page
        );
    }
}

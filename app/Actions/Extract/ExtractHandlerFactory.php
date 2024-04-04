<?php

declare(strict_types=1);

namespace App\Actions\Extract;

use App\Actions\Extract\Handlers\JapanWikiExtractHandler;
use App\Actions\Extract\Handlers\PortalExtractHandler;
use App\Actions\Extract\Handlers\TwitransExtractHandler;
use App\Enums\SiteName;
use Generator;

class ExtractHandlerFactory
{
    /**
     * @param  array<int,SiteName>  $siteNames
     * @return Generator<int,\App\Actions\Extract\Handlers\ExtractHandlerInterface>
     */
    public function create(array $siteNames): Generator
    {
        foreach ($siteNames as $siteName) {
            yield match ($siteName) {
                SiteName::Japan => app(JapanWikiExtractHandler::class),
                SiteName::Twitrans => app(TwitransExtractHandler::class),
                SiteName::Portal => app(PortalExtractHandler::class),
            };
        }
    }
}

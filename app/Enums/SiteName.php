<?php

declare(strict_types=1);

namespace App\Enums;

enum SiteName: string
{
    case SimutransAddonPortal = 'portal';
    case SimutransJapanWiki = 'japan';
    case TwitransWiki = 'twitrans';
}

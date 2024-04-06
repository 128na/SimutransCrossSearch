<?php

declare(strict_types=1);

use App\Enums\PakSlug;
use App\Enums\SiteName;

return [
    PakSlug::Pak64->value => 'Pak64',
    PakSlug::Pak128->value => 'Pak128',
    PakSlug::Pak128Jp->value => 'Pak128.Japan',
    SiteName::Japan->value => '日本語wiki',
    SiteName::Twitrans->value => '実験室',
    SiteName::Portal->value => 'Portal',
];

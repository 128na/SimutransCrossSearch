<?php

declare(strict_types=1);

namespace App\Enums;

enum PakSlug: string
{
    case Pak64 = '64';

    case Pak128 = '128';

    case Pak128Jp = '128-japan';
}

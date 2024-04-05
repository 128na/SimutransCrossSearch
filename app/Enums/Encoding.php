<?php

declare(strict_types=1);

namespace App\Enums;

enum Encoding: string
{
    case UTF_8 = 'utf-8';
    case EUC_JP = 'euc-jp';
}

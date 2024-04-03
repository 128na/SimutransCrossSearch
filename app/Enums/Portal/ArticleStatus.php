<?php

declare(strict_types=1);

namespace App\Enums\Portal;

/**
 * @see https://github.com/128na/simutrans-portal/blob/master/app/Enums/ArticleStatus.php
 */
enum ArticleStatus: string
{
    /**
     * 公開
     */
    case Publish = 'publish';
}

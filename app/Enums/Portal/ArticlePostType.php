<?php

declare(strict_types=1);

namespace App\Enums\Portal;

/**
 * @see https://github.com/128na/simutrans-portal/blob/master/app/Enums/ArticlePostType.php
 */
enum ArticlePostType: string
{
    /**
     * アドオン投稿
     */
    case AddonPost = 'addon-post';
    /**
     * アドオン紹介
     */
    case AddonIntroduction = 'addon-introduction';
}

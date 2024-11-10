<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $raw_page_id
 * @property SiteName $site_name
 * @property string $url
 * @property string $text
 * @property string $title
 * @property \Carbon\CarbonImmutable $last_modified 元記事の最終更新日時
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Pak> $paks
 * @property-read int|null $paks_count
 * @property-read \App\Models\RawPage $rawPage
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page query()
 * @mixin \Eloquent
 */
	final class Page extends \Eloquent implements \Spatie\Feed\Feedable {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property PakSlug $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Page> $pages
 * @property-read int|null $pages_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pak newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pak newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pak query()
 * @mixin \Eloquent
 */
	final class Pak extends \Eloquent {}
}

namespace App\Models\Portal{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string $title タイトル
 * @property string $slug スラッグ
 * @property ArticlePostType $post_type 投稿形式
 * @property array $contents コンテンツ
 * @property ArticleStatus $status 公開状態
 * @property int $pr PR記事
 * @property \Carbon\CarbonImmutable|null $published_at 投稿日時
 * @property \Carbon\CarbonImmutable|null $modified_at 更新日時
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Portal\Category> $categories
 * @property-read int|null $categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Portal\Tag> $tags
 * @property-read int|null $tags_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article query()
 * @mixin \Eloquent
 */
	final class Article extends \Eloquent {}
}

namespace App\Models\Portal{
/**
 * 
 *
 * @property int $id
 * @property CategoryType $type 分類
 * @property string $slug スラッグ
 * @property int $need_admin 管理者専用カテゴリ
 * @property int $order 表示順
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category query()
 * @mixin \Eloquent
 */
	final class Category extends \Eloquent {}
}

namespace App\Models\Portal{
/**
 * 
 *
 * @property int $id
 * @property int $attachment_id
 * @property string $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FileInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FileInfo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FileInfo query()
 * @mixin \Eloquent
 */
	final class FileInfo extends \Eloquent {}
}

namespace App\Models\Portal{
/**
 * 
 *
 * @property int $id
 * @property string $name タグ名
 * @property string|null $description 説明
 * @property int $editable 1:編集可,0:編集不可
 * @property int|null $created_by
 * @property int|null $last_modified_by
 * @property string|null $last_modified_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag query()
 * @mixin \Eloquent
 */
	final class Tag extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property SiteName $site_name
 * @property string $url
 * @property string $html
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Page|null $page
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RawPage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RawPage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RawPage query()
 * @mixin \Eloquent
 */
	final class RawPage extends \Eloquent {}
}


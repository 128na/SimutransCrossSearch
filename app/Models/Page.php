<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SiteName;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;

/**
 * @property int $id
 * @property int $raw_page_id
 * @property SiteName $site_name
 * @property string $url
 * @property string $text
 * @property string $title
 * @property CarbonImmutable $last_modified 元記事の最終更新日時
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Pak> $paks
 * @property-read int|null $paks_count
 * @property-read RawPage $rawPage
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page query()
 *
 * @mixin \Eloquent
 */
final class Page extends Model implements Feedable
{
    protected $fillable = [
        'site_name',
        'url',
        'title',
        'text',
        'last_modified',
    ];

    protected $casts = [
        'site_name' => SiteName::class,
        'last_modified' => 'immutable_datetime',
    ];

    /**
     * @return BelongsToMany<Pak,$this>
     */
    public function paks(): BelongsToMany
    {
        return $this->belongsToMany(Pak::class);
    }

    /**
     * @return BelongsTo<RawPage,$this>
     */
    public function rawPage(): BelongsTo
    {
        return $this->belongsTo(RawPage::class);
    }

    public function getSummary(int $length = 100): string
    {
        return mb_strimwidth($this->text, 0, $length, '...');
    }

    #[\Override]
    public function toFeedItem(): FeedItem
    {
        return FeedItem::create()
            ->id((string) $this->id)
            ->title($this->title)
            ->summary($this->getSummary())
            ->updated($this->last_modified)
            ->link($this->url)
            ->authorName(__('misc.'.$this->site_name->value));
    }
}

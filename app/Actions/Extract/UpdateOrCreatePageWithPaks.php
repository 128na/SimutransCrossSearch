<?php

declare(strict_types=1);

namespace App\Actions\Extract;

use App\Enums\PakSlug;
use App\Models\Page;
use App\Models\RawPage;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

/**
 * Page の更新と Pak の同期を 1 トランザクションで行う。
 *
 * 別々に呼ぶと SyncPak が失敗した際に「title/text は新しいが Pak 紐付けは古いまま」の
 * 半端な Page が検索/Feed に見えてしまう（D4: 抽出の原子性）。
 */
final readonly class UpdateOrCreatePageWithPaks
{
    public function __construct(
        private UpdateOrCreatePage $updateOrCreatePage,
        private SyncPak $syncPak,
    ) {}

    /**
     * @param  array<int,PakSlug>  $paks
     */
    public function __invoke(RawPage $rawPage, string $title, string $text, CarbonImmutable $lastModified, array $paks): Page
    {
        return DB::transaction(function () use ($rawPage, $title, $text, $lastModified, $paks): Page {
            $page = ($this->updateOrCreatePage)($rawPage, $title, $text, $lastModified);
            ($this->syncPak)($page, $paks);

            return $page;
        });
    }
}

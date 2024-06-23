<?php

declare(strict_types=1);

namespace App\Actions\Extract;

use App\Models\Page;
use App\Models\Pak;
use Illuminate\Support\Collection;

final class SyncPak
{
    /**
     * @var null|Collection<int|string,int>
     */
    private ?Collection $collection = null;

    /**
     * @param  array<int,\App\Enums\PakSlug>  $paks
     */
    public function __invoke(Page $page, array $paks): void
    {
        $pakIds = $this->resolvePakIds($paks);
        $page->paks()->sync($pakIds);
    }

    /**
     * @param  array<int,\App\Enums\PakSlug>  $paks
     * @return array<int,int>
     */
    private function resolvePakIds(array $paks): array
    {
        $result = [];
        foreach ($paks as $pak) {
            $resolved = $this->getPaks()->get($pak->value);
            if ($resolved) {
                $result[] = $resolved;
            }
        }

        return $result;
    }

    /**
     * @return Collection<int|string,int>
     */
    private function getPaks(): Collection
    {
        if (! $this->collection instanceof \Illuminate\Support\Collection) {
            $this->collection = Pak::pluck('id', 'slug');
        }

        return $this->collection;
    }
}

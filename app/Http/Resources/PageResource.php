<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Page;
use App\Models\Pak;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class PageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        assert($this->resource instanceof Page);

        return [
            'title' => $this->resource->title,
            'site' => __('misc.'.$this->resource->site_name->value),
            'paks' => $this->resource->paks->map(fn (Pak $pak) => __('misc.'.$pak->slug->value)),
            'url' => $this->resource->url,
            'last_modified' => $this->resource->last_modified->toIso8601String(),
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class Pages extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($item) {
            return [
                'site' => $item->display_site_name,
                'title' => $item->title,
                'url' => $item->url,
                'paks' => $item->paks->pluck('name'),
                'last_modified' => $item->last_modified ? $item->last_modified->toISOString() : '?',
            ];
        });
    }
}

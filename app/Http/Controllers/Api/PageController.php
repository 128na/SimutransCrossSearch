<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\SearchPage\SearchAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\PageSearchRequest;
use App\Http\Resources\PageResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class PageController extends Controller
{
    public function index(PageSearchRequest $pageSearchRequest, SearchAction $searchAction): AnonymousResourceCollection
    {
        /**
         * @var array{keyword:string,paks:array<int,int|string>,sites:array<int,string>}
         */
        $data = $pageSearchRequest->validated();

        return PageResource::collection($searchAction($data));
    }
}

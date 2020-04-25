<?php

namespace App\Factories;

use App\Services\MediaService\MediaService;
use App\Services\MediaService\SmileVideoMediaService;
use App\Services\MediaService\TwitterMediaService;
use App\Services\MediaService\YoutubeMediaService;
use Exception;

class MediaServiceFactory
{
    public function make($name): MediaService
    {
        switch ($name) {
            case 'youtube':
                return app(YoutubeMediaService::class);
            case 'nico':
                return app(SmileVideoMediaService::class);
            case 'twitter':
                return app(TwitterMediaService::class);

            default:
                throw new Exception("{$name} is not defined MediaService", 1);

        }
    }
}

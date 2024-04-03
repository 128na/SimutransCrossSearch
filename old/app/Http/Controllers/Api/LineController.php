<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\Line\WebhookHandler;
use Illuminate\Http\Request;

class LineController extends Controller
{
    public function webhook(Request $request)
    {
        $body = $request->getContent();
        $signature = $request->header('x-line-signature');
        $channelSecret = config('services.line.channel_secret');

        WebhookHandler::dispatchAfterResponse($channelSecret, $signature, $body);

        return response('', 200);
    }
}

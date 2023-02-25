<?php

namespace App\Services\Line;

use Illuminate\Support\Facades\Http;

class LineClient
{
    public function __construct()
    {
    }

    public function reply(Event $event, array $messages): void
    {
        $res = Http::withToken(config('services.line.channel_access_token'))
            ->withHeaders(['X-Line-Retry-Key' => $event->getWebhookEventId()])
            ->post('https://api.line.me/v2/bot/message/reply', [
                'replyToken' => $event->getReplyToken(),
                'messages' => $messages,
            ]);
        logger()->channel('line-bot')->debug('api response', [$res->status(), $res->body()]);
    }
}

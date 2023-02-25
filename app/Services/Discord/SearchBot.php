<?php

namespace App\Services\Discord;

use App\Services\PageSearchService;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

class SearchBot
{
    public function __construct(
        private TimeoutableDiscord $client,
        private MessageParser $messageParser,
        private PageSearchService $pageSearchService,
    ) {
    }

    public function handle(): void
    {
        $this->client->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) {
            logger()->channel('discord-bot')->info('Event::MESSAGE_CREATE', [
                'message' => $message->content,
                'user_id' => $message->author->id,
                'bot' => $message->author->bot,
            ]);

            if ($this->shouldWork($message)) {
                $word = $this->messageParser->parseRawWord($message);
                $type = $this->messageParser->parseType($message);
                $condition = $this->pageSearchService->parseSearchCondition($word, $type);
                $paks = $this->messageParser->parsePaks($message);
                logger()->channel('discord-bot')->info('search condition', [
                    'condition' => $condition,
                    'paks' => $paks,
                ]);
                $result = $this->pageSearchService->search($condition, $paks, 5);
                $body = sprintf('「%s」の検索結果 %d 件', $word, $result->total());
                foreach ($result as $page) {
                    $body .= sprintf("\n[%s] %s %s", $page->site_name, $page->title, $page->url);
                }
                if ($result->count()) {
                    $body .= sprintf("\n続き %s", route('pages.search', [
                        'paks' => $paks,
                        'type' => $type,
                        'word' => $word,
                    ]));
                }

                $message->reply($body);

                return;
            }
        });
        $this->client->run();
    }

    private function shouldWork(Message $message): bool
    {
        if ($message->user_id === config('services.discord.client_id')) {
            dump('self message');

            return false;
        }
        if ($message->author->bot) {
            dump('bot message');

            return false;
        }

        return str_starts_with($message->content, config('services.discord.trigger'));
    }
}

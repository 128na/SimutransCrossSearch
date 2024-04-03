<?php

namespace App\Jobs\Line;

use App\Services\Line\Event;
use App\Services\Line\LineClient;
use App\Services\Line\MessageParser;
use App\Services\Line\SignatureValidator;
use App\Services\PageSearchService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WebhookHandler implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private string $channelSecret,
        private string $signature,
        private string $body,
    ) {
    }

    public function handle(
        SignatureValidator $signatureValidator,
        PageSearchService $pageSearchService,
        MessageParser $messageParser,
        LineClient $lineClient,
    ): void {
        $this->validate($signatureValidator);
        $events = $this->getEvents();

        foreach ($events as $event) {
            try {
                if ($event->getType() === 'message') {
                    $this->handleEvent(
                        $event,
                        $messageParser,
                        $pageSearchService,
                        $lineClient,
                    );
                }
            } catch (\Throwable $th) {
                report($th);
            }
        }
    }

    private function validate(SignatureValidator $signatureValidator): void
    {
        $signatureValidator->validate($this->channelSecret, $this->signature, $this->body);
    }

    /**
     * @return array<Event>
     */
    private function getEvents(): array
    {
        $messages = json_decode($this->body, true);
        logger()->channel('line-bot')->debug('messages', $messages);

        return array_map(fn (array $e): Event => new Event($e), $messages['events'] ?? []);
    }

    private function handleEvent(
        Event $event,
        MessageParser $messageParser,
        PageSearchService $pageSearchService,
        LineClient $lineClient,
    ): void {
        $word = $messageParser->parseRawWord($event->getText());
        $type = $messageParser->parseType($word);
        $condition = $pageSearchService->parseSearchCondition($word, $type);
        $paks = $messageParser->parsePaks($word);
        logger()->channel('line-bot')->info('search condition', [
            'condition' => $condition,
            'paks' => $paks,
        ]);

        $result = $pageSearchService->search($condition, $paks, 5);
        $count = $result->total();

        $messages = [
            [
                'type' => 'text',
                'text' => sprintf('「%s」の検索結果 %d 件', $word, $count),
            ],
        ];

        if ($count) {
            $body = '';
            foreach ($result as $page) {
                $body .= sprintf("\n[%s] %s %s", $page->site_name, $page->title, $page->url);
            }
            $messages[] = [
                'type' => 'text',
                'text' => $body,
            ];
        }

        if ($count > 5) {
            $messages[] = [
                'type' => 'text',
                'text' => sprintf("\n続き %s", route('pages.search', [
                    'paks' => $paks,
                    'type' => $type,
                    'word' => $word,
                ])),
            ];
        }

        $lineClient->reply($event, $messages);
    }
}

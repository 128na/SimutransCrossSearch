<?php

namespace App\Services\Discord;

use Discord\Parts\Channel\Message;

class MessageParser
{
    public function __construct(
    ) {
    }

    /**
     * @return array<string>
     */
    public function parsePaks(Message $message): array
    {
        return array_map('strval', array_keys(config('paks')));
    }

    public function parseType(Message $message): string
    {
        return 'and';
    }

    public function parseRawWord(Message $message): string
    {
        return trim(str_replace(config('services.discord.trigger'), '', $message->content));
    }
}

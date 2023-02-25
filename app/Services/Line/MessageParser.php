<?php

namespace App\Services\Line;

class MessageParser
{
    public function __construct(
    ) {
    }

    /**
     * @return array<string>
     */
    public function parsePaks(string $message): array
    {
        return array_map('strval', array_keys(config('paks')));
    }

    public function parseType(string $message): string
    {
        return 'and';
    }

    public function parseRawWord(string $message): string
    {
        return trim($message);
    }
}

<?php

namespace App\Services\Discord;

use Discord\Discord;

class TimeoutableDiscord extends Discord
{
    private int $startAt;

    public function __construct(array $options = [], private int $timeout = 10)
    {
        $this->startAt = time();
        parent::__construct($options);
    }

    /**
     * Process WebSocket message payloads.
     *
     * @param  string  $data Message payload.
     */
    protected function processWsMessage(string $data): void
    {
        $this->handleTimeout();
        parent::processWsMessage($data);
    }

    private function handleTimeout(): void
    {
        $now = time();
        if ($this->startAt + $this->timeout <= $now) {
            throw new TimeoutException(sprintf('time out %d sec passed.', $now - $this->startAt));
        }
    }
}

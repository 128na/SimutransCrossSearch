<?php

namespace App\Services\Line;

use Exception;

class Event
{
    public function __construct(private array $event)
    {
    }

    public function getType(): string
    {
        return $this->event['type'] ?? '';
    }

    public function getText(): string
    {
        return isset($this->event['message']['text'])
            ? $this->event['message']['text']
            : '';
    }

    public function getUserId(): string
    {
        return isset($this->event['source']['userId'])
            ? $this->event['source']['userId']
            : '';
    }

    public function getReplyToken(): string
    {
        return $this->event['replyToken'] ?? '';
    }

    public function getWebhookEventId(): string
    {
        if (isset($this->event['webhookEventId'])) {
            return $this->event['webhookEventId'];
        }
        throw new Exception('missing webhookEventId');
    }

    public function isRedelivery(): bool
    {
        return isset($this->event['deliveryContext']['isRedelivery'])
            ? $this->event['deliveryContext']['isRedelivery']
            : false;
    }
}

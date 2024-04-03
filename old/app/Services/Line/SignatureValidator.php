<?php

namespace App\Services\Line;

class SignatureValidator
{
    public function __construct()
    {
    }

    public function validate(string $channelSecret, string $signature, string $body): void
    {
        $hash = hash_hmac('sha256', $body, $channelSecret, true);
        $actual = base64_encode($hash);

        if (hash_equals($signature, $actual) === false) {
            throw new InvalidSignatureException();
        }
    }
}

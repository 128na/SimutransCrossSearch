<?php

declare(strict_types=1);

namespace App\Actions\Logging;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

final class LoggingApi
{
    public function __invoke(string $title, FormRequest $formRequest): void
    {
        Log::channel('api')->info($title, [
            'remoteAddr' => $formRequest->server('REMOTE_ADDR', 'N/A'),
            'referer' => $formRequest->server('HTTP_REFERER', 'N/A'),
            'UA' => $formRequest->server('HTTP_USER_AGENT', 'N/A'),
            'data' => $formRequest->validated(),
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Actions\Logging;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

final class LoggingApi
{
    public function __invoke(string $title, FormRequest $request): void
    {
        Log::channel('api')->info($title, [
            'remoteAddr' => $request->server('REMOTE_ADDR', 'N/A'),
            'referer' => $request->server('HTTP_REFERER', 'N/A'),
            'UA' => $request->server('HTTP_USER_AGENT', 'N/A'),
            'data' => $request->validated(),
        ]);
    }
}

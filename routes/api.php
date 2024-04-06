<?php

declare(strict_types=1);

use App\Http\Controllers\Api\PageController;
use Illuminate\Support\Facades\Route;

Route::middleware(['throttle:api'])->group(function (): void {
    Route::get('/v2/search', (new PageController())->index(...));
});

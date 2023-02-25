<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

use App\Http\Controllers\Api\LineController;
use App\Http\Controllers\Api\v1\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/v1/search', [PageController::class, 'search'])->name('api.v1.search');
Route::post('/line/webhook', [LineController::class, 'webhook'])->name('api.line.webhook');

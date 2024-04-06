<?php

declare(strict_types=1);

use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', (new PageController())->index(...));

<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

final class PageController extends Controller
{
    public function index(): View
    {
        return view('index');
    }
}

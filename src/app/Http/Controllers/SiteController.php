<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\rssable;
use App\Models\EloquentRss;

class SiteController extends Controller
{
  use rssable;

  public function __construct()
  {
    $this->readerInit();
  }

  public function index()
  {
    $rsses = EloquentRss::all();

    return view('site', compact('rsses'));
  }
}

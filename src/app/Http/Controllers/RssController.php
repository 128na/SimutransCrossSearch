<?php

namespace App\Http\Controllers;

use App\Models\EloquentRss;
use App\Traits\CRUDable;

class RssController extends Controller
{
  use CRUDable;

  public function __construct()
  {
    $this->title      = 'Rss';
    $this->model_name = EloquentRss::class;
    $this->route      = 'rss';
    $this->fields     = ['url' => 'text'];
    $this->validation = [
      'url' => 'required|url|unique:rsses,url',
    ];
  }
}

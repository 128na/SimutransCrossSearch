<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
    $this->fields     = [
      'url' => 'text',
      'active' => 'radio',
    ];
    $this->options    = [
      'active' => [
        '有効' => '1',
        '無効' => '0',
      ],
    ];
    $this->validation = [
      'store' => [
        'url'    => 'required|url|unique:rsses,url',
        'active' => 'required|boolean',
      ],
      'update' => [
        'url'    => 'required|url',
        'active' => 'required|boolean',
      ],
    ];
  }
}

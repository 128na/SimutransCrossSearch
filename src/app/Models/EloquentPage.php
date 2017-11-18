<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EloquentPage extends Model
{
  protected $table = 'pages';
  protected $fillable = ['site_name', 'url', 'title', 'text'];
}

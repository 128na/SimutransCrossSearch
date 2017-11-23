<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EloquentRss extends Model
{
  protected $table = 'rsses';
  protected $fillable = ['url'];
  protected $hidden = ['url', 'created_at', 'updated_at'];
}

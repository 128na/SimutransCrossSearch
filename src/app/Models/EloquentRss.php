<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EloquentRss extends Model
{
  protected $table    = 'rsses';
  protected $fillable = ['url', 'active'];
  protected $casts    = ['active' => 'boolean'];
  protected $hidden   = ['url', 'created_at', 'updated_at', 'active'];

  public function scopeActive($query)
  {
    return $query->where('active', true);
  }

}

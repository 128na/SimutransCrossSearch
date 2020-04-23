<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SearchLog extends Model
{
    protected $fillable = ['query', 'count'];

    protected $casts = [
        'count' => 'int',
    ];
    protected $attributes = [
        'count' => 1,
    ];
}

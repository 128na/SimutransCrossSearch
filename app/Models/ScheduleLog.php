<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleLog extends Model
{
    const STATUS_BEGIN = 'begin';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';

    protected $fillable = ['label', 'status'];

    public static function begin($label)
    {
        return self::create(['label' => $label]);
    }
    public static function end($label, $status = self::STATUS_SUCCESS)
    {
        return self::create(['label' => $label, 'status' => $status]);
    }
}

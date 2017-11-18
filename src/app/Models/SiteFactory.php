<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteFactory extends Model
{
  public static function forge($name)
  {
    foreach (array_keys(config('const.sites')) as $site_name) {
      if ($site_name === $name) {
        $class_name = "App\\Models\\Site{$site_name}";
        return new $class_name;
      }
    }
    throw new \Exception('site name not exists', 1);
  }
}

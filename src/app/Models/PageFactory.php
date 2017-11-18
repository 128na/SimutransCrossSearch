<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Models\PageSimutransWiki;

class PageFactory extends Model
{
  public static function forge($name, $url)
  {
    foreach (array_keys(config('const.sites')) as $site_name) {
      if ($site_name === $name) {
        $class_name = "App\\Models\\Page{$site_name}";
        return new $class_name($url, config("const.sites.{$site_name}.name"));
      }
    }
    throw new \Exception('site name not exists', 1);
  }
}

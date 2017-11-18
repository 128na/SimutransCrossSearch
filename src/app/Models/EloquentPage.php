<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EloquentPage extends Model
{
  protected $table = 'pages';
  protected $fillable = ['site_name', 'url', 'title', 'text', 'pak'];

  public function expectText($word = null)
  {
    $len = 400;

    if ($word) {
      $pos = mb_stripos($this->text, $word, 0, 'UTF-8');
      $pos   = ($pos !== false) ? $pos : 0;
      $begin = 2 * ($pos - $len / 2);
      $begin = ($begin > 0) ? $begin : 0;
      $res   = mb_strimwidth($this->text, $begin, $len, '...', 'UTF-8');
      return ($begin > 0) ? '...'.$res : $res;
    } else {
      return mb_strimwidth($this->text, 0, $len, '...', 'UTF-8');
    }
  }

  public function getPakName()
  {
    return str_replace(array_values(config('const.pak')), array_keys(config('const.pak')), $this->pak);
  }
}

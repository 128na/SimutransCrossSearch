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

    if (is_null($word)) {
      return mb_strimwidth($this->text, 0, $len, '...');
    } else {
      $pos = mb_stripos($this->text, $word);
      $pos   = ($pos !== false) ? $pos : 0;
      $begin = $pos - $len;
      $begin = ($begin > 0) ? $begin : 0;
      $res = mb_strimwidth($this->text, $begin, $len, '...');
      return ($begin > 0) ? '...'.$res : $res;
    }
  }
}

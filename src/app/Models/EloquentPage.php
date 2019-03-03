<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EloquentPage extends Model
{
  protected $table = 'pages';
  protected $fillable = ['site_name', 'url', 'title', 'text', 'pak'];

  public function expectText($word = '')
  {
    if (is_string($word)) {
      $len = 100;

      if ($word && mb_strpos($this->text, $word) !== false) {
        $texts = explode($word, $this->text);

        $pre = static::mb_strrev(mb_strimwidth(
                static::mb_strrev(array_shift($texts)), 0, $len, '...'), 'UTF-8');
        $suf = mb_strimwidth(array_shift($texts), 0, $len, '...', 'UTF-8');

        return "{$pre}{$word}{$suf}";
      } else {
        return mb_strimwidth($this->text, 0, $len, '...', 'UTF-8');
      }
    } else {
      return $word->map([$this, 'expectText'])->implode('...');
    }
  }

  public function getPakName()
  {
    return str_replace(array_values(config('const.pak')), array_keys(config('const.pak')), $this->pak);
  }

  private function mb_strrev($str)
  {
    $arr = preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
    return implode(array_reverse($arr));
  }
}

<?php
namespace App\Models;

/**
 * 検索ワード
 * 検索文字列をパースする
 */
class SearchWords
{
  private $words;

  public function __construct($word_str = null)
  {
      $this->words = static::parse($word_str);

  }

  private static function clean($str)
  {
    $str = str_replace('　', ' ', $str);
    $str = str_replace('＆', '&', $str);
    $str = trim($str);
    return $str;
  }

  /**
   * 検索ワードをand,orで抽出する
   * @example hoge foo&bar -> [[hoge],[foo,bar]] -> hoge or (foo and bar)
   */
  private static function parse($word_str)
  {
    $word_str = static::clean($word_str);
    $words = collect(explode(' ', $word_str));

    $words = $words->map(function($w) {
      return collect(explode('&', $w));
    });
    return $words;
  }

  /**
   * return word collection
   */
  public function getWordsCollection()
  {
    return $this->words;
  }

  /**
   * return flatten words array
   */
  public function getFlattenWords()
  {
    return $this->words->flatten();
  }
}

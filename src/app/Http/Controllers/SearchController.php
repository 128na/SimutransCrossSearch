<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EloquentPage as Page;

class SearchController extends Controller
{
  public function index(Request $request)
  {
    return view('welcome');
  }
  public function search(Request $request)
  {
    $data = [
      'words' => collect(),
      'highlight_words' => [],
      'pak' => '',
      'paks' => array_flip(config('const.pak')),
    ];
    $query = Page::query();

    if ($request->filled('word')) {
      $word = $request->input('word');
      $words = static::parseWord($word);
      $query = static::buildWordQuery($query, $words);
      $data['word'] = $word;
      $data['words'] = $words;
      $data['highlight_words'] = $words->flatten();
    }

    if ($request->filled('pak')) {
      $pak  = $request->input('pak');
      $pak_id = config('const.pak.'.$request->input('pak'));
      $query->where('pak', 'like', "%{$pak_id}%");
      $data['pak'] = $pak;
    }

    $data['pages'] = $query->get();
    $data['condition_text'] = static::conditionText($data['words'], $data['pak']);

    return view('search', $data);
  }

  private function conditionText($words, $pak)
  {
    $text = '';
    if ($pak) {
      $text .= "pak{$pak} ";
    }

    $text .= $words->map(function($and_words) {
      return $and_words->implode('かつ');
    })->implode('または');

    return $text;
  }

  private static function cleanWord($word)
  {
    $word = str_replace('　', ' ', $word);
    $word = str_replace('＆', '&', $word);
    $word = trim($word);
    return $word;
  }

  /**
   * 検索ワードをand,orで抽出する
   * @example hoge foo&bar -> [[hoge],[foo,bar]] -> hoge or (foo and bar)
   */
  private static function parseWord($word)
  {
    $word = static::cleanWord($word);
    $words = collect(explode(' ', $word));

    $words = $words->map(function($w) {
      return collect(explode('&', $w));
    });
    return $words;
  }

  private static function buildWordQuery($query, $words)
  {
    $words->each(function($and_words) use ($query) {
      $query->orWhere(function($or_query) use ($and_words) {
        $and_words->each(function($word) use ($or_query) {
          $or_query->where(function($and_query) use($word) {
            $and_query->where('text', 'like', "%{$word}%")
              ->orWhere('title', 'like', "%{$word}%");
          });
        });
      });
    });
    return $query;
  }
}

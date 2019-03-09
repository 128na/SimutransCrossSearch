<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EloquentPage as Page;
use App\Models\SearchWords;

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
      $data['word'] = $request->input('word');

      $words = new SearchWords($data['word']);
      $data['words'] = $words->getWordsCollection();
      $query = static::buildWordSearchQuery($query, $data['words']);
      $data['highlight_words'] = $words->getFlattenWords();
    }

    if ($request->filled('pak')) {
      $pak  = $request->input('pak');
      $pak_id = config('const.pak.'.$request->input('pak'));
      $query->where('pak', 'like', "%{$pak_id}%");
      $data['pak'] = $pak;
    }

    $data['pages'] = $query->get();
    $data['condition_text'] = static::buildConditionText($data['words'], $data['pak']);

    return view('search', $data);
  }

  private function buildConditionText($words, $pak)
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


  private static function buildWordSearchQuery($query, $words)
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

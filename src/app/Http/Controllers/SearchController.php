<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EloquentPage as Page;

class SearchController extends Controller
{
  public function index(Request $reqest)
  {
    return view('welcome');
  }
  public function search(Request $reqest)
  {
    $word = $reqest->input('word');
    $pak  = $reqest->input('pak');

    $query = Page::where('pak', 'like', "%{$pak}");
    $conds = ["「{$pak}」"];

    if ($word) {
      $query->where(function($query) use($word) {
        $query->where('text', 'like', "%{$word}%")
              ->orWhere('title', 'like', "%{$word}%");
      });
      $conds[] = "「{$word}」";
    }

    $pages = $query->get();
    return view('search', compact('pages', 'word', 'pak', 'conds'));
  }
}

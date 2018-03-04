<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\rssable;
use App\Models\EloquentRss;

class SiteController extends Controller
{
  use rssable;

  public function __construct()
  {
    $this->readerInit();
  }

  public function index(Request $request)
  {
    $rsses = EloquentRss::active()->get();
    return view('site', compact('rsses'));
  }

  public function store(Request $request)
  {
    $request->validate([
      'url' => 'required|url|unique:rsses,url',
    ]);

    // rss取得できないURLは申し訳ないがNG
    if (!$this->fetch($request->input('url'))) {
      $request->session()->flash('error', '無効なRSSフィールドです');
      return redirect()
        ->withInput()
        ->route('sites');
    }

    // 有効化は管理者がする
    EloquentRss::create([
      'url'    => $request->input('url'),
      'active' => false,
    ]);

    $request->session()->flash('success', '登録が完了しました。管理者の確認後に反映されます。');
    return redirect()->route('sites');
  }
}

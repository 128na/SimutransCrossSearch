<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\EloquentPage as Page;
use App\Models\Twitter;

class TweetController extends Controller
{

  private $from;
  private $text;
  private $tweet_id;
  private $twitter;
    //
  public function index(Request $request)
  {
    static::validateToken($request->input('token'));

    $this->from     = $request->input('from');
    $this->text     = Twitter::cleanText($request->input('text'));
    $this->tweet_id = Twitter::cleanId($request->input('LinkToTweet'));

    logger('from:'.$this->from);
    logger('text:'.$this->text);

    if ($this->isHelp()) {
      return $this->replyHelp();
    }
    if ($this->isRandom()) {
      return $this->replyRandom();
    }

    return $this->replySearchResult();
  }


  private function isHelp()
  {
    return strtolower($this->text) === 'help';
  }


  private function isRandom()
  {
    return $this->text === '';
  }

  private function reply($message)
  {
    $twitter = new Twitter();
    $res = $twitter->reply($message, $this->from, $this->tweet_id);
    logger("processed : {$res->id}");

    return response('OK', 200);
  }

  private function replyHelp()
  {
    $message = <<<EOM
使い方
このアカウントにリプライするとアドオン検索などができます。
help …このメッセージを表示
空リプ …ランダムにアドオンを紹介
アドオン名 …アドオンを検索
EOM;

    return $this->reply($message);
  }


  private function replyRandom()
  {
    $page = Page::inRandomOrder()->first();

    $message = "ランダムにアドオンを検索するよ\n{$page->title}( {$page->url} )";
    return $this->reply($message);
  }


  private function replySearchResult()
  {
    $pages = $this->searchByWord();

    $message = $this->buildSearchResultMessage($pages);

    return $this->reply($message);
  }


  private static function validateToken($token)
  {
    if($token !== env('IFTTT_TOKEN')) {
      logger('invalid token');
      exit;
    }
  }


  private function buildSearchResultMessage($pages)
  {
    $message = now()."現在 \n";
    $message .= "「{$this->text}」での検索結果\n";

    if ($pages->count() === 0) {
      $message .= '該当なし';
      return $message;
    }

    // 各pakサイズでの該当件数をリスト表示する
    $message .= collect(static::analyzeResult($pages))
      ->map(function($count, $pakname) {
        return "pak{$pakname}:{$count}件 ";
      })->implode("\n");
    // 検索リンク
    $message .= "\n検索結果一覧はこちら→ ".route('search', ['word' => $this->text]);

    return $message;
  }


  private static function analyzeResult($pages)
  {
    return $pages->reduce(function($current, $item) {
      $pak_name = $item->getPakName();
      if (array_key_exists($pak_name, $current)) {
        $current[$pak_name] ++;
      } else {
        $current[$pak_name] = 1;
      }
      return $current;
    }, []);
  }


  private function searchByWord()
  {
    return Page::where(function($query) {
      $query->where('text', 'like', "%{$this->text}%")
        ->orWhere('title', 'like', "%{$this->text}%");
    })->get();
  }
}

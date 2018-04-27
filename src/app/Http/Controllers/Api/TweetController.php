<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\EloquentPage as Page;
use App\Models\Twitter;

class TweetController extends Controller
{
    //
  public function index(Request $request)
  {
    static::validateToken($request->input('token'));

    $from = $request->input('from');
    $text = Twitter::cleanText($request->input('text'));
    $tweet_id = Twitter::cleanId($request->input('LinkToTweet'));

    logger('from:'.$from);
    logger('text:'.$text);

    $twitter = new Twitter();

    $pages = static::searchByWord($text);

    $message = static::buildMessage($from, $text, $pages);

    $res = $twitter->reply($message, $tweet_id);

    logger("processed : {$res->id}");
  }


  private static function validateToken($token)
  {
    if($token !== env('IFTTT_TOKEN')) {
      logger('invalid token');
      exit;
    }
  }

  private function buildMessage($from, $text, $pages)
  {
    $message = "@{$from} ";
    $message .= now()."現在 \n";
    $message .= "「{$text}」での検索結果\n";

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
    $message .= "\n検索結果一覧はこちら→ ".route('search', ['word' => $text]);

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


  private static function searchByWord($word)
  {
    return Page::where(function($query) use ($word) {
      $query->where('text', 'like', "%{$word}%")
        ->orWhere('title', 'like', "%{$word}%");
    })->get();
  }
}

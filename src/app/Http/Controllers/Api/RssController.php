<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\rssable;
use App\Models\EloquentRss;

class RssController extends Controller
{
  use rssable;

  public function __construct()
  {
    $this->readerInit();
  }

  public function index()
  {
    $res = [
      'sites' => [],
      'data'  => [],
      'error' => [],
    ];
    foreach (config('const.rss') as $id => $site) {
      $res['sites'][$id] = [
        'name' => $site['name'],
        'url'  => config("const.sites.{$site['name']}.url"),
      ];
      // RSS取得
      if ($this->fetch($site['url'])) {
        foreach ($this->feed->get_items() as $item) {
          $res['data'][] = [
            'sid'   => $id,
            'title' => $item->get_title(),
            'link'  => $item->get_link(),
            'time'  => (int)$item->get_date('U'),
          ];
        }
        $res['sites'][$id]['count'] = count($this->feed->get_items());
      }else{
        logger()->error('RSS取得失敗 '.$this->feed->error());
        $res['error'][] = ['site_id' => $id, 'message' => '取得失敗'];
      }
    }
    // 日付降順
    usort($res['data'], function($a,$b) {return $b['time'] <=> $a['time'];});
    return $res;
  }

  public function site($id)
  {
    $site = EloquentRss::where('id', $id)->active()->firstOrFail();

    // RSS取得
    abort_unless($this->fetch($site->url), 404);
    $latest_item = $this->feed->get_items()[0];

    $data = [
      'site'   => [
        'name' =>$this->feed->get_title(),
        'url'  => $this->feed->get_link(),
      ],
      'title' => $latest_item->get_title(),
      'link'  => $latest_item->get_link(),
      'time'  => (int)$latest_item->get_date('U'),
    ];
    return $data;
  }
}

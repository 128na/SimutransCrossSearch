<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SimplePie;

class RssController extends Controller
{
  public function __construct()
  {
    $this->readerInit(new SimplePie());
  }

    //
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
        'url'  => $site['url'],
      ];
      // RSS取得
      $this->feed->set_feed_url($site['url']);
      $success = $this->feed->init();
      if ($success){
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

  private function readerInit(SimplePie $instance)
  {
    $this->feed = $instance;
    $this->feed->enable_cache(true);
    $this->feed->set_cache_duration(5 * 60 * 60);  //sec
    $this->feed->set_cache_location(storage_path('framework/cache/rss'));
    $this->feed->handle_content_type();
  }
}

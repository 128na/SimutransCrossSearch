<?php
namespace App\Traits;

use SimplePie;

trait Rssable {

  protected function readerInit()
  {
    $this->feed = new SimplePie();
    $this->feed->enable_cache(true);
    $this->feed->set_cache_duration(config('const.app.rss_expired'));  //sec
    $this->feed->set_cache_location(storage_path(config('const.app.rss_cache')));
    $this->feed->handle_content_type();
  }

  public function fetch($url)
  {
    $this->feed->set_feed_url($url);
    return $this->feed->init();
  }
}

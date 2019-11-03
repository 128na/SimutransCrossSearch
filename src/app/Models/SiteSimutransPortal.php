<?php

namespace App\Models;

use App\Models\Site;

class SiteSimutransPortal extends Site
{
  public function __construct()
  {
    $this->base_url = config('const.sites.SimutransPortal.url');
    parent::__construct();
  }

  protected function getListPageUrl()
  {
    return $this->base_url.'/api/v1/articles?token='.env('API_TOKEN');
  }

  protected function extractUrls($crawler)
  {
    // url一覧を取得
    $urls = $crawler->filter('ul li')->each(function ($node) {
      return $node->text();
    });

    // urlデコード
    $urls = array_map(function($url) {
      return urldecode($url);
    }, $urls);

    $this->setUrls($urls);
  }
}

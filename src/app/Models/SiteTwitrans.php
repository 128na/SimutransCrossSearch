<?php

namespace App\Models;

use App\Models\Site;

class SiteTwitrans extends Site
{
  public function __construct()
  {
    $this->base_url = config('const.sites.Twitrans.url');
    parent::__construct();
  }

  protected function getListPageUrl()
  {
    return $this->base_url.'/?cmd=list';
  }

  protected function extractUrls($crawler)
  {
    // url一覧を取得
    $urls = $crawler->filter('#body ul li ul li')->each(function ($node) {
      return str_replace('/twitrans', $this->base_url, $node->filter('a')->attr('href'));
    });

    // アドオン関係のみフィルタ
    $urls = array_filter($urls, function($url) {
      return preg_match('/\/addon\/pak(64|128|128\.japan)\//', $url)
          ;
    });

    // 特定の不要ページを除外
    $urls = array_filter($urls, function($url) {
      return !preg_match('/Campany/', $url)
          && !preg_match('/Notice/', $url)
          && !preg_match('/MenuBar/', $url)
          && !preg_match('/make_support/', $url)
          && !preg_match('/companyIndex/', $url)
          && !preg_match('/複製/', $url)
          && !preg_match('/test/', $url)
          && !preg_match('/Index/', $url)
          ;
    });

    $this->setUrls($urls);
  }
}

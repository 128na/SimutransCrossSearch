<?php

namespace App\Models;

use App\Models\Site;

class SiteJapaneseSimutrans extends Site
{
  public function __construct()
  {
    $this->base_url = config('const.sites.JapaneseSimutrans.url');
    parent::__construct();
  }

  protected function getListPageUrl()
  {
    return $this->base_url.'/?cmd=list';
  }

  protected function extractUrls($crawler)
  {
    // url一覧を取得
    $urls = $crawler->filter('#body > ul li')->each(function ($node) {
      return $node->filter('a')->attr('href');
    });

    // 文字コード変換
    $urls = array_map(function($url) {
      return mb_convert_encoding(urldecode($url), 'UTF-8', 'EUC-JP');
    }, $urls);

    // アドオン関係のみフィルタ
    $urls = array_filter($urls, function($url) {
      return preg_match('/\?Addon128\//', $url)
          || preg_match('/\?Addon128Japan\//', $url)
          || preg_match('/\?アドオン\//', $url)
          ;
    });

    // 特定の不要ページを除外
    $urls = array_filter($urls, function($url) {
      return !preg_match('/MenuBar/', $url)
          && !preg_match('/Index/', $url)
          && !preg_match('/header/', $url)
          && !preg_match('/投稿報告/', $url)
          && !preg_match('/問題報告/', $url)
          && !preg_match('/Addon128Japan\/.*(車|他)/', $url)
          ;
    });

    $this->setUrls($urls);
  }
}

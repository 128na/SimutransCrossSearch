<?php

namespace App\Models;

use App\Traits\Scrapable;

class Site
{
  use Scrapable;

  // ベースurl
  protected $base_url = '';

  // 詳細ページ絶対url一覧
  private $urls = [];

  public function __construct()
  {
    $this->scrapeInit();
  }

  // 一覧ページから情報を取得する
  public function scrape()
  {
    $crawler = $this->getClient()->request('GET', $this->getListPageUrl());

    $this->extractUrls($crawler);
    return $this;
  }

  protected function extractUrls($crawler)
  {
    throw new \Exception('未実装', 1);
  }
  protected function getListPageUrl()
  {
    throw new \Exception('未実装', 1);
  }
  public function getUrls()
  {
    return $this->urls;
  }
  public function setUrls($val)
  {
    $this->urls = $val;
  }
}

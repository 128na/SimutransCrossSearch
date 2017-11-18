<?php

namespace App\Models;

use App\Traits\Scrapable;
use App\Models\EloquentPage;

class Page
{
  use Scrapable;

  private $url;
  private $site_name;
  private $title;
  private $text;

  private $model_name = EloquentPage::class;
  private $instance;

  public function __construct($url, $site_name)
  {
    $this->url = $url;
    $this->site_name = $site_name;
    $this->scrapeInit();

    $this->instance = $this->model_name::firstOrNew(['url' => $url], ['site_name' => $site_name]);
  }

  // 詳細ページから情報を取得する
  public function scrape() {
    $craweler = $this->getClient()->request('GET', $this->url);

    $this->extractTitle($craweler);
    $this->extractText($craweler);

    $this->trimText();
    return $this;
  }

  protected function extractTitle($craweler) {
    $this->title = $craweler->filter('title')->text();
  }

  protected function extractText($craweler) {
    $this->text = $craweler->text();
  }

  private function trimText()
  {
    $this->text = preg_replace('/(\s)+/', '$1', $this->text);
  }
  public function save()
  {
    $this->instance->fill([
      'title' => $this->title,
      'text'  => $this->text,
    ])->save();
  }

  public function setTitle($val) {$this->title = $val;}
  public function setText($val) {$this->text = $val;}
  public function getTitle() {return $this->title;}
  public function getText() {return $this->text;}
}

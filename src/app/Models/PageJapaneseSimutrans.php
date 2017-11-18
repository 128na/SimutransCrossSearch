<?php

namespace App\Models;

use App\Models\Page;

class PageJapaneseSimutrans extends Page
{

  protected function extractTitle($crawler) {
    parent::extractTitle($crawler);
    $this->setTitle(str_replace(' - Simutrans日本語化･解説', '', $this->getTitle()));
  }

  protected function extractText($crawler) {
    $this->setText($crawler->filter('div.ie5')->text());
  }

  protected function extractPak($crawler)
  {
    $pak = 'pak64';
    if (stripos($this->getUrl(), 'Addon128Japan') !== false) {
      $pak = 'pak128.japan';
    } elseif (stripos($this->getUrl(), 'Addon128') !== false) {
      $pak = 'pak128_';
    }
    $this->setPak($pak);
  }
}

<?php

namespace App\Models;

use App\Models\Page;

class PageTwitrans extends Page
{

  protected function extractTitle($crawler) {
    parent::extractTitle($crawler);
    $this->setTitle(str_replace(' - Simutrans的な実験室 Wiki*', '', $this->getTitle()));
  }

  protected function extractText($crawler) {
    $texts = $crawler->filter('#body .ie5')->each(function($n, $i) {
      return $n->text();
    });
    $this->setText(implode("\n", $texts));
  }

  protected function extractPak($crawler)
  {
    $pak = 'pak64';
    if (stripos($this->getUrl(), 'pak128.japan') !== false) {
      $pak = 'pak128.japan';
    } elseif (stripos($this->getUrl(), 'pak128') !== false) {
      $pak = 'pak128';
    }
    $this->setPak($pak);
  }
}

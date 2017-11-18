<?php

namespace App\Models;

use App\Models\Page;

class PageSimutransPortal extends Page
{

  protected function extractTitle($crawler) {
    parent::extractTitle($crawler);
    $this->setTitle(str_replace('  |  Simutransポータル', '', $this->getTitle()));
  }

  protected function extractText($crawler) {
    $this->setText($crawler->filter('article')->text());
  }

  protected function extractPak($crawler)
  {
    $paks = $crawler->filter('.pak')->each(function($node) {
      $pak = $node->text();
      if ($pak === 'pak128') {
        $pak = 'pak128_';
      }
      return $pak;
    });
    $this->setPak(implode(',', $paks));
  }
}

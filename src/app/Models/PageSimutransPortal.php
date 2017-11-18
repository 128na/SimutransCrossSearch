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

      switch ($node->text()) {
        case 'pak128':
          return config('const.pak.128');
        case 'pak128.japan':
          return config('const.pak.128japan');
        default:
          config('const.pak.64');
      }
    });
    $this->setPak(implode(',', $paks));
  }
}

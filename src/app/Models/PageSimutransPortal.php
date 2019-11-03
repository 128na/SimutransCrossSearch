<?php

namespace App\Models;

use App\Models\Page;

class PageSimutransPortal extends Page
{

  protected function extractTitle($crawler) {
    $this->setTitle($crawler->filter('h1')->text());
  }

  protected function extractText($crawler) {
    $this->setText($crawler->filter('.article')->text());
  }

  protected function extractPak($crawler)
  {
    $paks = $crawler->filter('.badge-danger')->each(function($node) {

      switch (trim($node->text())) {
        case 'Pak128':
          return config('const.pak.128');
        case 'Pak128.japan':
          return config('const.pak.128japan');
        default:
          return config('const.pak.64');
      }
    });
    $this->setPak(implode(',', $paks));
  }
}

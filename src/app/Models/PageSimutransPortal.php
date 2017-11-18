<?php

namespace App\Models;

use App\Models\Page;

class PageSimutransPortal extends Page
{

  public function extractTitle($craweler) {
    parent::extractTitle($craweler);
    $this->setTitle(str_replace('  |  Simutransポータル', '', $this->getTitle()));
  }

  public function extractText($craweler) {
    $this->setText($craweler->filter('article')->text());
  }
}

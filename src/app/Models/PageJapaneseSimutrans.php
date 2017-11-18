<?php

namespace App\Models;

use App\Models\Page;

class PageJapaneseSimutrans extends Page
{

  public function extractTitle($craweler) {
    parent::extractTitle($craweler);
    $this->setTitle(str_replace(' - Simutrans日本語化･解説', '', $this->getTitle()));
  }

  public function extractText($craweler) {
    $this->setText($craweler->filter('div.ie5')->text());
  }
}

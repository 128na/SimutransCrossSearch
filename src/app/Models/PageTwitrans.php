<?php

namespace App\Models;

use App\Models\Page;

class PageTwitrans extends Page
{

  public function extractTitle($craweler) {
    parent::extractTitle($craweler);
    $this->setTitle(str_replace(' - Simutrans的な実験室 Wiki*', '', $this->getTitle()));
  }

  public function extractText($craweler) {
    $this->setText($craweler->filter('#body')->text());
  }
}

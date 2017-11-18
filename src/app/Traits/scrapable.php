<?php
namespace App\Traits;

use Goutte\Client;

trait Scrapable {
  public function scrapeInit()
  {
    $this->client = new Client();
  }

  public function getClient() {
    return $this->client;
  }
}

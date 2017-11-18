<?php
namespace App\Traits;

use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;

trait Scrapable {
  public function scrapeInit()
  {
    $goutteClient = new Client();
    $guzzleClient = new GuzzleClient(array(
      'timeout' => 60,
    ));
    $goutteClient->setClient($guzzleClient);

    $this->client = $goutteClient;
  }

  public function getClient() {
    return $this->client;
  }
}

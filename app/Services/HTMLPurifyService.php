<?php
namespace App\Services;

use \HTMLPurifier;
use \HTMLPurifier_Config;

class HTMLPurifyService
{
    private HTMLPurifier $purifier;

    public function __construct()
    {
        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.AllowedElements', [
            'span',
        ]);
        $this->purifier = new HTMLPurifier($config);
    }

    public function purifyHTML(string $raw)
    {
        return $this->purifier->purify($raw);
    }
}

<?php

declare(strict_types=1);

namespace App\Actions\Extract;

use Illuminate\Log\Logger;

interface HandlerInterface
{
    public function __invoke(Logger $logger): void;
}

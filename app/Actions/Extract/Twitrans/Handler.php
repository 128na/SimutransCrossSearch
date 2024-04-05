<?php

declare(strict_types=1);

namespace App\Actions\Extract\Twitrans;

use App\Actions\Extract\HandlerInterface;
use Psr\Log\LoggerInterface;

class Handler implements HandlerInterface
{
    public function __invoke(LoggerInterface $logger): void
    {
    }
}

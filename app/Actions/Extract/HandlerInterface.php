<?php

declare(strict_types=1);

namespace App\Actions\Extract;

use Psr\Log\LoggerInterface;

interface HandlerInterface
{
    public function __invoke(LoggerInterface $logger): void;
}

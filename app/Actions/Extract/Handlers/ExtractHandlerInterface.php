<?php

declare(strict_types=1);

namespace App\Actions\Extract\Handlers;

interface ExtractHandlerInterface
{
    public function __invoke(): void;
}

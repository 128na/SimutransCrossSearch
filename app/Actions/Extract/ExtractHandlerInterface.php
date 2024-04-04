<?php

declare(strict_types=1);

namespace App\Actions\Extract;

interface ExtractHandlerInterface
{
    public function __invoke(): void;
}

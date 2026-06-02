<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Extract;

use App\Actions\Extract\ExtractAction;
use App\Actions\Extract\HandlerInterface;
use App\Actions\Extract\Japan\Handler;
use App\Enums\SiteName;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Tests\Feature\TestCase;

final class ExtractActionTest extends TestCase
{
    public function test_invokes_handlers_for_all_sites_when_null_provided(): void
    {
        $state = (object) ['japan' => false, 'portal' => false, 'twitrans' => false];

        $this->app->bind(Handler::class, fn (): HandlerInterface => new readonly class($state) implements HandlerInterface
        {
            public function __construct(private object $state) {}

            public function __invoke(LoggerInterface $logger): void
            {
                $this->state->japan = true;
            }
        });

        $this->app->bind(\App\Actions\Extract\Portal\Handler::class, fn (): HandlerInterface => new readonly class($state) implements HandlerInterface
        {
            public function __construct(private object $state) {}

            public function __invoke(LoggerInterface $logger): void
            {
                $this->state->portal = true;
            }
        });

        $this->app->bind(\App\Actions\Extract\Twitrans\Handler::class, fn (): HandlerInterface => new readonly class($state) implements HandlerInterface
        {
            public function __construct(private object $state) {}

            public function __invoke(LoggerInterface $logger): void
            {
                $this->state->twitrans = true;
            }
        });

        $extractAction = app(ExtractAction::class);
        $extractAction(null, new NullLogger);

        $this->assertTrue($state->japan);
        $this->assertTrue($state->portal);
        $this->assertTrue($state->twitrans);
    }

    public function test_invokes_specific_handler_when_site_provided(): void
    {
        $state = (object) ['japan' => false, 'portal' => false, 'twitrans' => false];

        $this->app->bind(Handler::class, fn (): HandlerInterface => new readonly class($state) implements HandlerInterface
        {
            public function __construct(private object $state) {}

            public function __invoke(LoggerInterface $logger): void
            {
                $this->state->japan = true;
            }
        });

        $this->app->bind(\App\Actions\Extract\Portal\Handler::class, fn (): HandlerInterface => new readonly class($state) implements HandlerInterface
        {
            public function __construct(private object $state) {}

            public function __invoke(LoggerInterface $logger): void
            {
                $this->state->portal = true;
            }
        });

        $this->app->bind(\App\Actions\Extract\Twitrans\Handler::class, fn (): HandlerInterface => new readonly class($state) implements HandlerInterface
        {
            public function __construct(private object $state) {}

            public function __invoke(LoggerInterface $logger): void
            {
                $this->state->twitrans = true;
            }
        });

        $extractAction = app(ExtractAction::class);
        $extractAction(SiteName::Japan, new NullLogger);

        $this->assertTrue($state->japan);
        $this->assertFalse($state->portal);
        $this->assertFalse($state->twitrans);
    }
}

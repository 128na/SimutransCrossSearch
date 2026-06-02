<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Extract;

use App\Actions\Extract\ExtractAction;
use App\Actions\Extract\HandlerInterface;
use App\Actions\Extract\Japan\Handler;
use App\Enums\SiteName;
use Psr\Log\NullLogger;
use Tests\Feature\TestCase;

final class ExtractActionTest extends TestCase
{
    public function test_invokes_handlers_for_all_sites_when_null_provided(): void
    {
        $japanHandler = \Mockery::mock(HandlerInterface::class);
        $japanHandler->shouldReceive('__invoke')->once();
        $this->app->instance(Handler::class, $japanHandler);

        $portalHandler = \Mockery::mock(HandlerInterface::class);
        $portalHandler->shouldReceive('__invoke')->once();
        $this->app->instance(\App\Actions\Extract\Portal\Handler::class, $portalHandler);

        $twitransHandler = \Mockery::mock(HandlerInterface::class);
        $twitransHandler->shouldReceive('__invoke')->once();
        $this->app->instance(\App\Actions\Extract\Twitrans\Handler::class, $twitransHandler);

        $action = app(ExtractAction::class);
        $action(null, new NullLogger);
    }

    public function test_invokes_specific_handler_when_site_provided(): void
    {
        $japanHandler = \Mockery::mock(HandlerInterface::class);
        $japanHandler->shouldReceive('__invoke')->once();
        $this->app->instance(Handler::class, $japanHandler);

        $portalHandler = \Mockery::mock(HandlerInterface::class);
        $portalHandler->shouldReceive('__invoke')->never();
        $this->app->instance(\App\Actions\Extract\Portal\Handler::class, $portalHandler);

        $twitransHandler = \Mockery::mock(HandlerInterface::class);
        $twitransHandler->shouldReceive('__invoke')->never();
        $this->app->instance(\App\Actions\Extract\Twitrans\Handler::class, $twitransHandler);

        $action = app(ExtractAction::class);
        $action(SiteName::Japan, new NullLogger);
    }
}

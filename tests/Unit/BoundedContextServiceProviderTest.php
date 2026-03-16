<?php

declare(strict_types=1);

namespace ComplexHeart\Tests\Unit;

use ComplexHeart\Infrastructure\Laravel\BoundedContextServiceProvider;
use Illuminate\Support\ServiceProvider;

function createMockApp(bool $runningInConsole = false): \Mockery\MockInterface
{
    $dispatcher = \Mockery::mock(\Illuminate\Contracts\Events\Dispatcher::class);
    $dispatcher->shouldReceive('listen')->byDefault();

    $router = \Mockery::mock();
    $router->shouldReceive('middleware')->andReturnSelf()->byDefault();
    $router->shouldReceive('group')->byDefault();

    $app = \Mockery::mock(\Illuminate\Contracts\Foundation\Application::class, \ArrayAccess::class);
    $app->shouldReceive('runningInConsole')->andReturn($runningInConsole)->byDefault();
    $app->shouldReceive('make')->with('events')->andReturn($dispatcher)->byDefault();
    $app->shouldReceive('make')->with('router')->andReturn($router)->byDefault();

    $app->dispatcher = $dispatcher;
    $app->router = $router;

    return $app;
}

it('should extend Laravel ServiceProvider', function () {
    expect(is_subclass_of(BoundedContextServiceProvider::class, ServiceProvider::class))
        ->toBeTrue();
});

it('should register event listeners on boot', function () {
    $app = createMockApp();

    $app->dispatcher->shouldReceive('listen')
        ->once()
        ->with('App\\Events\\OrderPlaced', 'App\\Listeners\\SendConfirmation');

    $provider = new class ($app) extends BoundedContextServiceProvider {
        protected array $events = [
            'App\\Events\\OrderPlaced' => 'App\\Listeners\\SendConfirmation',
        ];
    };

    $provider->boot();
});

it('should register multiple listeners for a single event', function () {
    $app = createMockApp();
    $app->dispatcher->shouldReceive('listen')->twice();

    $provider = new class ($app) extends BoundedContextServiceProvider {
        protected array $events = [
            'App\\Events\\OrderPlaced' => [
                'App\\Listeners\\SendConfirmation',
                'App\\Listeners\\UpdateInventory',
            ],
        ];
    };

    $provider->boot();
});

it('should register console commands when running in console', function () {
    $app = createMockApp(runningInConsole: true);

    $provider = new class ($app) extends BoundedContextServiceProvider {
        protected array $commands = [
            'App\\Console\\SyncCommand',
        ];

        public function commands($commands): void
        {
            $this->testCommands = $commands;
        }

        public array $testCommands = [];
    };

    $provider->boot();

    expect($provider->testCommands)->toBe(['App\\Console\\SyncCommand']);
});

it('should not register commands when not running in console', function () {
    $app = createMockApp(runningInConsole: false);

    $provider = new class ($app) extends BoundedContextServiceProvider {
        protected array $commands = [
            'App\\Console\\SyncCommand',
        ];

        public function commands($commands): void
        {
            $this->testCommands = $commands;
        }

        public array $testCommands = [];
    };

    $provider->boot();

    expect($provider->testCommands)->toBe([]);
});

it('should boot without errors when all arrays are empty', function () {
    $app = createMockApp();

    $provider = new class ($app) extends BoundedContextServiceProvider {
    };

    $provider->boot();

    expect(true)->toBeTrue();
});

it('should load routes grouped by middleware', function () {
    $app = createMockApp();

    $router = \Mockery::mock();
    $router->shouldReceive('middleware')->with('web')->once()->andReturnSelf();
    $router->shouldReceive('middleware')->with('api')->once()->andReturnSelf();
    $router->shouldReceive('group')->twice();

    $app->shouldReceive('make')->with('router')->andReturn($router);

    $provider = new class ($app) extends BoundedContextServiceProvider {
        protected array $routes = [
            'web' => ['/tmp/web.php'],
            'api' => ['/tmp/api.php'],
        ];
    };

    $provider->boot();
});

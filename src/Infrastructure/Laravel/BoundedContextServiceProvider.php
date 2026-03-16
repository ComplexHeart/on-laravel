<?php

declare(strict_types=1);

namespace ComplexHeart\Infrastructure\Laravel;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

/**
 * Class BoundedContextServiceProvider
 *
 * Base service provider for bounded contexts in a modular monolith. Each
 * bounded context should extend this class and declare its own bindings,
 * event listeners, commands, migrations, and routes.
 *
 * @author Unay Santisteban <usantisteban@othercode.io>
 */
abstract class BoundedContextServiceProvider extends ServiceProvider
{
    /**
     * Domain event to listener mappings.
     *
     * @var array<class-string, class-string|array<class-string>>
     */
    protected array $events = [];

    /**
     * Console commands provided by this context.
     *
     * @var array<int, class-string>
     */
    protected array $commands = [];

    /**
     * Migration paths for this context.
     *
     * @var array<int, string>
     */
    protected array $migrations = [];

    /**
     * Route definitions grouped by middleware.
     *
     * Example:
     *   ['web' => [__DIR__ . '/Http/Routes/web.php']]
     *   ['api' => [__DIR__ . '/Http/Routes/api.php']]
     *
     * @var array<string, array<int, string>>
     */
    protected array $routes = [];

    public function boot(): void
    {
        $this->bootMigrations();
        $this->bootRoutes();
        $this->bootEvents();
        $this->bootCommands();
    }

    protected function bootMigrations(): void
    {
        if ($this->migrations !== []) {
            $this->loadMigrationsFrom($this->migrations);
        }
    }

    protected function bootRoutes(): void
    {
        /** @var Router $router */
        $router = $this->app->make('router');

        foreach ($this->routes as $middleware => $files) {
            foreach ($files as $file) {
                $router->middleware($middleware)->group($file);
            }
        }
    }

    protected function bootEvents(): void
    {
        /** @var Dispatcher $dispatcher */
        $dispatcher = $this->app->make('events');

        foreach ($this->events as $event => $listeners) {
            $listeners = is_array($listeners) ? $listeners : [$listeners];
            foreach ($listeners as $listener) {
                $dispatcher->listen($event, $listener);
            }
        }
    }

    protected function bootCommands(): void
    {
        if ($this->app->runningInConsole() && $this->commands !== []) {
            $this->commands($this->commands);
        }
    }
}

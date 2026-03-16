# Bounded Context Service Provider

The `BoundedContextServiceProvider` is a base class for organizing your Laravel application as a modular monolith. Each bounded context gets its own service provider that declares bindings, event listeners, console commands, migrations, and routes.

## Basic Usage

Extend `BoundedContextServiceProvider` and declare your context's dependencies:

```php
use ComplexHeart\Infrastructure\Laravel\BoundedContextServiceProvider;

class CompanyRegistryServiceProvider extends BoundedContextServiceProvider
{
    public array $bindings = [
        CompanyRepository::class => EloquentCompanyRepository::class,
        IndustryRepository::class => EloquentIndustryRepository::class,
    ];

    protected array $events = [
        CompanyCreated::class => SendCompanyWelcome::class,
    ];

    protected array $commands = [
        SyncCompaniesCommand::class,
    ];

    protected array $migrations = [
        __DIR__ . '/Companies/Infrastructure/Persistence/Migrations',
        __DIR__ . '/Industries/Infrastructure/Persistence/Migrations',
    ];

    protected array $routes = [
        'web' => [__DIR__ . '/Shared/Infrastructure/Http/Routes/web.php'],
        'api' => [__DIR__ . '/Shared/Infrastructure/Http/Routes/api.php'],
    ];
}
```

Register the provider in `bootstrap/app.php` (Laravel 11+) or `config/app.php`:

```php
// bootstrap/providers.php
return [
    App\CompanyRegistry\CompanyRegistryServiceProvider::class,
    App\IdentityAndAccess\IdentityAndAccessServiceProvider::class,
];
```

## Declarative Arrays

### `$bindings`

Standard Laravel `$bindings` — maps interfaces to implementations for this context:

```php
public array $bindings = [
    UserRepository::class => EloquentUserRepository::class,
];
```

### `$events`

Maps domain events to listeners. Supports a single listener or an array of listeners per event:

```php
protected array $events = [
    // Single listener
    UserCreated::class => SendWelcomeEmail::class,

    // Multiple listeners
    OrderPlaced::class => [
        SendOrderConfirmation::class,
        UpdateInventory::class,
        NotifyWarehouse::class,
    ],
];
```

### `$commands`

Console commands provided by this context. Only registered when running in console:

```php
protected array $commands = [
    SyncUsersCommand::class,
    PruneExpiredTokensCommand::class,
];
```

### `$migrations`

Migration paths for this context:

```php
protected array $migrations = [
    __DIR__ . '/Users/Infrastructure/Persistence/Migrations',
];
```

### `$routes`

Route files grouped by middleware:

```php
protected array $routes = [
    'web' => [__DIR__ . '/Http/Routes/web.php'],
    'api' => [__DIR__ . '/Http/Routes/api.php'],
];
```

## Extending Boot

All boot methods are `protected`, so you can override or extend them:

```php
class IdentityAndAccessServiceProvider extends BoundedContextServiceProvider
{
    protected array $events = [
        UserEmailUpdated::class => SendEmailVerification::class,
    ];

    public function boot(): void
    {
        parent::boot();

        // Context-specific boot logic
        $this->configureFortify();
    }

    private function configureFortify(): void
    {
        Fortify::createUsersUsing(CreateUser::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
    }
}
```

## Project Structure

A typical modular monolith using `BoundedContextServiceProvider`:

```
app/
├── CompanyRegistry/
│   ├── CompanyRegistryServiceProvider.php
│   ├── Companies/
│   │   ├── Domain/
│   │   │   ├── Company.php
│   │   │   ├── Contracts/
│   │   │   │   └── CompanyRepository.php
│   │   │   └── Events/
│   │   │       └── CompanyCreated.php
│   │   ├── Application/
│   │   │   └── CreateCompany.php
│   │   └── Infrastructure/
│   │       ├── Persistence/
│   │       │   ├── EloquentCompanyRepository.php
│   │       │   └── Migrations/
│   │       └── Http/
│   │           ├── Controllers/
│   │           └── Requests/
│   └── Shared/
│       └── Infrastructure/
│           └── Http/
│               └── Routes/
│                   ├── web.php
│                   └── api.php
├── IdentityAndAccess/
│   ├── IdentityAndAccessServiceProvider.php
│   └── Users/
│       ├── Domain/
│       ├── Application/
│       └── Infrastructure/
```

Each context is self-contained — if you need to extract one into a microservice later, the provider already defines everything it needs.

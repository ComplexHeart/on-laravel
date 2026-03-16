# Eloquent Factory

The `EloquentFactory` bridges Laravel's factory system with ComplexHeart's domain model construction. When a model provides a static `new()` factory method (from the `IsModel` trait), it is used instead of plain instantiation so that domain invariants are validated during test seeding.

## The Problem

Laravel factories use `new Model($attributes)` to create instances, which bypasses ComplexHeart's `Model::new()` static factory. This means domain invariants are not validated during test seeding — factories can create invalid domain objects.

## The Solution

Extend `EloquentFactory` instead of Laravel's `Factory`:

```php
use ComplexHeart\Infrastructure\Laravel\Persistence\EloquentFactory;

/**
 * @extends EloquentFactory<User>
 */
class UserFactory extends EloquentFactory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid(),
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
        ];
    }
}
```

## How It Works

`EloquentFactory` overrides the `newModel()` method:

- If the model has a static `new()` method → uses `Model::new(...$attributes)` (domain construction with invariant validation)
- If the model does not have `new()` → falls back to `new Model($attributes)` (standard Eloquent construction)

This means you can use `EloquentFactory` for all your models, regardless of whether they use ComplexHeart traits or not.

## Usage

Factories work exactly like standard Laravel factories:

```php
// Single instance
$user = User::factory()->create();

// Multiple instances
$users = User::factory()->count(5)->create();

// With overrides
$admin = User::factory()->create([
    'role' => 'admin',
    'email' => 'admin@example.com',
]);

// Without persisting
$user = User::factory()->make();
```

## Connecting Factory to Model

Add the `HasFactory` trait to your model and point it to your factory:

```php
use Illuminate\Database\Eloquent\Factories\HasFactory;
use ComplexHeart\Domain\Model\IsAggregate;

class User extends Model implements Aggregate
{
    use IsAggregate;
    use HasFactory;

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }
}
```

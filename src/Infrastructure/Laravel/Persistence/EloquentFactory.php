<?php

declare(strict_types=1);

namespace ComplexHeart\Infrastructure\Laravel\Persistence;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class EloquentFactory
 *
 * Abstract factory that bridges Laravel's factory system with ComplexHeart's
 * domain model construction. When the model provides a static new() factory
 * method (from IsModel trait), it is used instead of plain instantiation so
 * that domain invariants are validated during test seeding.
 *
 * @template TModel of Model
 *
 * @extends Factory<TModel>
 *
 * @author Unay Santisteban <usantisteban@othercode.io>
 */
abstract class EloquentFactory extends Factory
{
    public function newModel(array $attributes = [])
    {
        $model = $this->modelName();

        return method_exists($model, 'new')
            ? $model::new(...$attributes)
            : new $model($attributes);
    }
}

<?php

declare(strict_types=1);

namespace ComplexHeart\Tests\Unit;

use ComplexHeart\Infrastructure\Laravel\Persistence\EloquentFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

it('should extend Laravel Factory', function () {
    expect(is_subclass_of(EloquentFactory::class, Factory::class))
        ->toBeTrue();
});

it('should use static new() factory when model supports it', function () {
    $factory = new class () extends EloquentFactory {
        protected $model = \ComplexHeart\Tests\Fixtures\ModelWithNew::class;

        public function definition(): array
        {
            return ['name' => 'test'];
        }
    };

    $model = $factory->newModel(['name' => 'hello']);

    expect($model)->toBeInstanceOf(\ComplexHeart\Tests\Fixtures\ModelWithNew::class)
        ->and($model->createdViaNew)->toBeTrue()
        ->and($model->name)->toBe('hello');
});

it('should use plain constructor when model does not have new()', function () {
    $factory = new class () extends EloquentFactory {
        protected $model = \ComplexHeart\Tests\Fixtures\ModelWithoutNew::class;

        public function definition(): array
        {
            return ['name' => 'test'];
        }
    };

    $model = $factory->newModel(['name' => 'hello']);

    expect($model)->toBeInstanceOf(\ComplexHeart\Tests\Fixtures\ModelWithoutNew::class)
        ->and($model->createdViaNew)->toBeFalse();
});

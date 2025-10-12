<?php

declare(strict_types=1);

use ComplexHeart\Domain\Criteria\Contracts\PaginatedResult;
use ComplexHeart\Domain\Criteria\Criteria;
use ComplexHeart\Domain\Criteria\FilterGroup;
use ComplexHeart\Tests\Fixtures\Infrastructure\Persistence\Laravel\Sources\UserDatabaseSource;
use ComplexHeart\Tests\Fixtures\Infrastructure\Persistence\Laravel\UsersEloquentRepository;

beforeEach(function () {
    $this->createApplication();

    UserDatabaseSource::create([
        'first_name' => 'Vincent',
        'last_name' => 'Vega',
        'email' => 'vincent.vega@complexheart.com',
        'bio' => 'And you know what they call a... a... a Quarter Pounder with Cheese in Paris?',
    ]);

    UserDatabaseSource::create([
        'first_name' => 'Jules',
        'last_name' => 'Winnfield',
        'email' => 'jules.winnfield@complexheart.com',
        'bio' => 'Say what again I dare you!',
    ]);
});

test('EloquentRepository should match given empty criteria and return PaginatedResult.', function () {
    $criteria = Criteria::default();

    $repo = new UsersEloquentRepository;
    $result = $repo->match($criteria);

    expect($result)->toBeInstanceOf(PaginatedResult::class)
        ->and($result->count())->toBe(2)
        ->and($result->total())->toBe(2)
        ->and($result->currentPage())->toBe(1)
        ->and($result->perPage())->toBe(25)
        ->and($result->isNotEmpty())->toBeTrue();
});

test('EloquentRepository should match given criteria and return PaginatedResult.', function () {
    $criteria = Criteria::default()
        ->withFilterGroup(FilterGroup::create()
            ->addFilterEqual('name', 'Vincent'));

    $repo = new UsersEloquentRepository;
    $result = $repo->match($criteria);

    expect($result)->toBeInstanceOf(PaginatedResult::class)
        ->and($result->count())->toBe(1)
        ->and($result->total())->toBe(1)
        ->and($result->isEmpty())->toBeFalse();
});

test('EloquentRepository should match given criteria and return empty PaginatedResult.', function () {
    $criteria = Criteria::default()
        ->withFilterGroup(FilterGroup::create()
            ->addFilterNotLike('email', '%@complexheart.com'));

    $repo = new UsersEloquentRepository;
    $result = $repo->match($criteria);

    expect($result)->toBeInstanceOf(PaginatedResult::class)
        ->and($result->count())->toBe(0)
        ->and($result->total())->toBe(0)
        ->and($result->isEmpty())->toBeTrue();
});

test('PaginatedResult extends Collection and supports collection methods.', function () {
    $criteria = Criteria::default();

    $repo = new UsersEloquentRepository;
    $result = $repo->match($criteria);

    // Should support Collection methods
    $names = $result->map(fn ($user) => $user->name)->toArray();
    expect($names)->toBeArray()->toHaveCount(2);

    $emails = $result->pluck('email')->toArray();
    expect($emails)->toContain('vincent.vega@complexheart.com');
});

test('PaginatedResult calculates pagination metadata correctly.', function () {
    // Create 30 additional users
    for ($i = 1; $i <= 30; $i++) {
        UserDatabaseSource::create([
            'first_name' => "User{$i}",
            'last_name' => "Test{$i}",
            'email' => "user{$i}@test.com",
            'bio' => "Bio {$i}",
        ]);
    }

    $criteria = Criteria::default()->withPageNumber(2, 10);

    $repo = new UsersEloquentRepository;
    $result = $repo->match($criteria);

    expect($result->total())->toBe(32) // 30 + 2 from beforeEach
        ->and($result->perPage())->toBe(10)
        ->and($result->currentPage())->toBe(2)
        ->and($result->lastPage())->toBe(4)
        ->and($result->hasMorePages())->toBeTrue()
        ->and($result->count())->toBe(10); // items on current page
});

<?php

declare(strict_types=1);


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
        'bio' => 'And you know what they call a... a... a Quarter Pounder with Cheese in Paris?'
    ]);

    UserDatabaseSource::create([
        'first_name' => 'Jules',
        'last_name' => 'Winnfield',
        'email' => 'jules.winnfield@complexheart.com',
        'bio' => 'Say what again I dare you!'
    ]);
});

test('EloquentRepository should match given empty criteria.', function () {
    $criteria = Criteria::default();

    $repo = new  UsersEloquentRepository();
    $users = $repo->match($criteria);

    expect($users)->toHaveCount(2);
});

test('EloquentRepository should match given criteria.', function () {
    $criteria = Criteria::default()
        ->withFilterGroup(FilterGroup::create()
            ->addFilterEqual('name', 'Vincent'));

    $repo = new  UsersEloquentRepository();
    $users = $repo->match($criteria);

    expect($users)->toHaveCount(1);
});

test('EloquentRepository should match given criteria and return empty collection.', function () {
    $criteria = Criteria::default()
        ->withFilterGroup(FilterGroup::create()
            ->addFilterNotLike('email', '%@complexheart.com'));

    $repo = new  UsersEloquentRepository();
    $users = $repo->match($criteria);

    expect($users)->toHaveCount(0);
});


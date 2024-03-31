<?php

declare(strict_types=1);

use ComplexHeart\Domain\Criteria\Criteria;
use ComplexHeart\Domain\Criteria\FilterGroup;
use ComplexHeart\Infrastructure\Laravel\Persistence\EloquentCriteriaParser;
use ComplexHeart\Tests\Fixtures\Infrastructure\Persistence\Laravel\Sources\UserDatabaseSource;

beforeEach(function () {
    $this->createApplication();
});

test('EloquentCriteriaParser should parse empty criteria.', function () {
    $parser = new EloquentCriteriaParser();

    $criteria = Criteria::default();
    $builder = $parser->applyCriteria(UserDatabaseSource::query(), $criteria);

    expect($builder->toRawSql())
        ->toBe('select * from "users" limit 25 offset 0');
});

test('EloquentCriteriaParser should parse equal filters.', function () {
    $parser = new EloquentCriteriaParser();

    $criteria = Criteria::default()
        ->withFilterGroup(FilterGroup::create()
            ->addFilterEqual('first_name', 'Vincent'));
    $builder = $parser->applyCriteria(UserDatabaseSource::query(), $criteria);

    expect($builder->toRawSql())
        ->toBe('select * from "users" where "first_name" = \'Vincent\' limit 25 offset 0');
});

test('EloquentCriteriaParser should parse not equal filter.', function () {
    $parser = new EloquentCriteriaParser();

    $criteria = Criteria::default()
        ->withFilterGroup(FilterGroup::create()
            ->addFilterNotEqual('first_name', 'Vincent'));
    $builder = $parser->applyCriteria(UserDatabaseSource::query(), $criteria);

    expect($builder->toRawSql())
        ->toBe('select * from "users" where "first_name" != \'Vincent\' limit 25 offset 0');
});

test('EloquentCriteriaParser should parse greater than filter.', function () {
    $parser = new EloquentCriteriaParser();

    $criteria = Criteria::default()
        ->withFilterGroup(FilterGroup::create()
            ->addFilterGreaterThan('stars', 5));
    $builder = $parser->applyCriteria(UserDatabaseSource::query(), $criteria);

    expect($builder->toRawSql())
        ->toBe('select * from "users" where "stars" > 5 limit 25 offset 0');
});

test('EloquentCriteriaParser should parse greater or equal than filter.', function () {
    $parser = new EloquentCriteriaParser();

    $criteria = Criteria::default()
        ->withFilterGroup(FilterGroup::create()
            ->addFilterGreaterOrEqualThan('stars', 5));
    $builder = $parser->applyCriteria(UserDatabaseSource::query(), $criteria);

    expect($builder->toRawSql())
        ->toBe('select * from "users" where "stars" >= 5 limit 25 offset 0');
});

test('EloquentCriteriaParser should parse less than filter.', function () {
    $parser = new EloquentCriteriaParser();

    $criteria = Criteria::default()
        ->withFilterGroup(FilterGroup::create()
            ->addFilterLessThan('stars', 5));
    $builder = $parser->applyCriteria(UserDatabaseSource::query(), $criteria);

    expect($builder->toRawSql())
        ->toBe('select * from "users" where "stars" < 5 limit 25 offset 0');
});

test('EloquentCriteriaParser should parse less or equal filter.', function () {
    $parser = new EloquentCriteriaParser();

    $criteria = Criteria::default()
        ->withFilterGroup(FilterGroup::create()
            ->addFilterLessOrEqualThan('stars', 5));
    $builder = $parser->applyCriteria(UserDatabaseSource::query(), $criteria);

    expect($builder->toRawSql())
        ->toBe('select * from "users" where "stars" <= 5 limit 25 offset 0');
});

test('EloquentCriteriaParser should parse in filter.', function () {
    $parser = new EloquentCriteriaParser();

    $criteria = Criteria::default()
        ->withFilterGroup(FilterGroup::create()
            ->addFilterIn('country', ['es', 'fr']));
    $builder = $parser->applyCriteria(UserDatabaseSource::query(), $criteria);

    expect($builder->toRawSql())
        ->toBe('select * from "users" where "country" in (\'es\', \'fr\') limit 25 offset 0');
});

test('EloquentCriteriaParser should parse not in filter.', function () {
    $parser = new EloquentCriteriaParser();

    $criteria = Criteria::default()
        ->withFilterGroup(FilterGroup::create()
            ->addFilterNotIn('country', ['es', 'fr']));
    $builder = $parser->applyCriteria(UserDatabaseSource::query(), $criteria);

    expect($builder->toRawSql())
        ->toBe('select * from "users" where "country" not in (\'es\', \'fr\') limit 25 offset 0');
});

test('EloquentCriteriaParser should parse like filter.', function () {
    $parser = new EloquentCriteriaParser();

    $criteria = Criteria::default()
        ->withFilterGroup(FilterGroup::create()
            ->addFilterLike('bio', '%developer%'));
    $builder = $parser->applyCriteria(UserDatabaseSource::query(), $criteria);

    expect($builder->toRawSql())
        ->toBe('select * from "users" where "bio" like \'%developer%\' limit 25 offset 0');
});

test('EloquentCriteriaParser should parse not like filter.', function () {
    $parser = new EloquentCriteriaParser();

    $criteria = Criteria::default()
        ->withFilterGroup(FilterGroup::create()
            ->addFilterNotLike('bio', '%developer%'));
    $builder = $parser->applyCriteria(UserDatabaseSource::query(), $criteria);

    expect($builder->toRawSql())
        ->toBe('select * from "users" where "bio" not like \'%developer%\' limit 25 offset 0');
});

test('EloquentCriteriaParser should parse contains (to like) filter.', function () {
    $parser = new EloquentCriteriaParser();

    $criteria = Criteria::default()
        ->withFilterGroup(FilterGroup::create()
            ->addFilterContains('bio', '%developer%'));
    $builder = $parser->applyCriteria(UserDatabaseSource::query(), $criteria);

    expect($builder->toRawSql())
        ->toBe('select * from "users" where "bio" like \'%developer%\' limit 25 offset 0');
});

test('EloquentCriteriaParser should parse not contains (to not like) filter.', function () {
    $parser = new EloquentCriteriaParser();

    $criteria = Criteria::default()
        ->withFilterGroup(FilterGroup::create()
            ->addFilterNotContains('bio', '%developer%'));
    $builder = $parser->applyCriteria(UserDatabaseSource::query(), $criteria);

    expect($builder->toRawSql())
        ->toBe('select * from "users" where "bio" not like \'%developer%\' limit 25 offset 0');
});

test('EloquentCriteriaParser should parse domain mapped attributes.', function () {
    $parser = new EloquentCriteriaParser([
        'name' => 'first_name',
        'surname' => 'last_name'
    ]);

    $criteria = Criteria::default()
        ->withFilterGroup(FilterGroup::create()
            ->addFilterEqual('name', 'Vincent'));
    $builder = $parser->applyCriteria(UserDatabaseSource::query(), $criteria);

    expect($builder->toRawSql())
        ->toBe('select * from "users" where "first_name" = \'Vincent\' limit 25 offset 0');
});

test('EloquentCriteriaParser should parse ASC ordering.', function () {
    $parser = new EloquentCriteriaParser();

    $criteria = Criteria::default()
        ->withOrderType('asc')
        ->withOrderBy('first_name');
    $builder = $parser->applyCriteria(UserDatabaseSource::query(), $criteria);

    expect($builder->toRawSql())
        ->toBe('select * from "users" order by "first_name" asc limit 25 offset 0');
});

test('EloquentCriteriaParser should parse DESC ordering.', function () {
    $parser = new EloquentCriteriaParser();

    $criteria = Criteria::default()
        ->withOrderType('desc')
        ->withOrderBy('last_name');
    $builder = $parser->applyCriteria(UserDatabaseSource::query(), $criteria);

    expect($builder->toRawSql())
        ->toBe('select * from "users" order by "last_name" desc limit 25 offset 0');
});

test('EloquentCriteriaParser should parse RANDOM ordering.', function () {
    $parser = new EloquentCriteriaParser();

    $criteria = Criteria::default()
        ->withOrderType('random')
        ->withOrderBy('first_name');
    $builder = $parser->applyCriteria(UserDatabaseSource::query(), $criteria);

    expect($builder->toRawSql())
        ->toBe('select * from "users" order by RANDOM() limit 25 offset 0');
});

test('EloquentCriteriaParser should parse 2 or more filters using AND operator.', function () {
    $parser = new EloquentCriteriaParser();

    $criteria = Criteria::default()
        ->withFilterGroup(FilterGroup::create()
            ->addFilterEqual('first_name', 'Vincent')
            ->addFilterEqual('last_name', 'Vega'));
    $builder = $parser->applyCriteria(UserDatabaseSource::query(), $criteria);

    expect($builder->toRawSql())
        ->toBe('select * from "users" where "first_name" = \'Vincent\' and "last_name" = \'Vega\' limit 25 offset 0');
});

test('EloquentCriteriaParser should parse 2 or more filters groups using OR operator.', function () {
    $parser = new EloquentCriteriaParser();

    $criteria = Criteria::default()
        ->withFilterGroup(FilterGroup::create()
            ->addFilterEqual('first_name', 'Vincent'))
        ->withFilterGroup(FilterGroup::create()
            ->addFilterEqual('last_name', 'Winnfield'));
    $builder = $parser->applyCriteria(UserDatabaseSource::query(), $criteria);

    expect($builder->toRawSql())
        ->toBe('select * from "users" where "first_name" = \'Vincent\' or ("last_name" = \'Winnfield\') limit 25 offset 0');
});

test('EloquentCriteriaParser should ignore Page object if limit is 0.', function () {
    $parser = new EloquentCriteriaParser();

    $criteria = Criteria::default()
        ->withPageLimit(0);
    $builder = $parser->applyCriteria(UserDatabaseSource::query(), $criteria);

    expect($builder->toRawSql())
        ->toBe('select * from "users"');
});
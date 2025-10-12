<?php

declare(strict_types=1);

use ComplexHeart\Domain\Criteria\Criteria;
use ComplexHeart\Domain\Criteria\FilterGroup;
use ComplexHeart\Domain\Criteria\Page;
use ComplexHeart\Infrastructure\Laravel\Persistence\EloquentCriteriaParser;
use ComplexHeart\Tests\Fixtures\Infrastructure\Persistence\Laravel\Sources\UserDatabaseSource;

beforeEach(function () {
    $this->createApplication();
});

test('EloquentCriteriaParser should parse empty criteria.', function () {
    $parser = new EloquentCriteriaParser;

    $criteria = Criteria::default();
    $result = $parser->applyCriteria(UserDatabaseSource::query(), $criteria);

    expect($result)->toBeArray()
        ->toHaveKeys(['builder', 'total', 'page', 'currentPage'])
        ->and($result['builder']->toRawSql())
        ->toBe('select * from "users" limit 25 offset 0');
});

test('EloquentCriteriaParser should parse equal filters.', function () {
    $parser = new EloquentCriteriaParser;

    $criteria = Criteria::default()
        ->withFilterGroup(FilterGroup::create()
            ->addFilterEqual('first_name', 'Vincent'));
    $result = $parser->applyCriteria(UserDatabaseSource::query(), $criteria);

    expect($result['builder']->toRawSql())
        ->toBe('select * from "users" where "first_name" = \'Vincent\' limit 25 offset 0');
});

test('EloquentCriteriaParser should parse not equal filter.', function () {
    $parser = new EloquentCriteriaParser;

    $criteria = Criteria::default()
        ->withFilterGroup(FilterGroup::create()
            ->addFilterNotEqual('first_name', 'Vincent'));
    $result = $parser->applyCriteria(UserDatabaseSource::query(), $criteria);

    expect($result['builder']->toRawSql())
        ->toBe('select * from "users" where "first_name" != \'Vincent\' limit 25 offset 0');
});

test('EloquentCriteriaParser should parse greater than filter.', function () {
    $parser = new EloquentCriteriaParser;

    $criteria = Criteria::default()
        ->withFilterGroup(FilterGroup::create()
            ->addFilterGreaterThan('stars', 5));
    $result = $parser->applyCriteria(UserDatabaseSource::query(), $criteria);

    expect($result['builder']->toRawSql())
        ->toBe('select * from "users" where "stars" > 5 limit 25 offset 0');
});

test('EloquentCriteriaParser should parse greater or equal than filter.', function () {
    $parser = new EloquentCriteriaParser;

    $criteria = Criteria::default()
        ->withFilterGroup(FilterGroup::create()
            ->addFilterGreaterOrEqualThan('stars', 5));
    $result = $parser->applyCriteria(UserDatabaseSource::query(), $criteria);

    expect($result['builder']->toRawSql())
        ->toBe('select * from "users" where "stars" >= 5 limit 25 offset 0');
});

test('EloquentCriteriaParser should parse less than filter.', function () {
    $parser = new EloquentCriteriaParser;

    $criteria = Criteria::default()
        ->withFilterGroup(FilterGroup::create()
            ->addFilterLessThan('stars', 5));
    $result = $parser->applyCriteria(UserDatabaseSource::query(), $criteria);

    expect($result['builder']->toRawSql())
        ->toBe('select * from "users" where "stars" < 5 limit 25 offset 0');
});

test('EloquentCriteriaParser should parse less or equal filter.', function () {
    $parser = new EloquentCriteriaParser;

    $criteria = Criteria::default()
        ->withFilterGroup(FilterGroup::create()
            ->addFilterLessOrEqualThan('stars', 5));
    $result = $parser->applyCriteria(UserDatabaseSource::query(), $criteria);

    expect($result['builder']->toRawSql())
        ->toBe('select * from "users" where "stars" <= 5 limit 25 offset 0');
});

test('EloquentCriteriaParser should parse in filter.', function () {
    $parser = new EloquentCriteriaParser;

    $criteria = Criteria::default()
        ->withFilterGroup(FilterGroup::create()
            ->addFilterIn('country', ['es', 'fr']));
    $result = $parser->applyCriteria(UserDatabaseSource::query(), $criteria);

    expect($result['builder']->toRawSql())
        ->toBe('select * from "users" where "country" in (\'es\', \'fr\') limit 25 offset 0');
});

test('EloquentCriteriaParser should parse not in filter.', function () {
    $parser = new EloquentCriteriaParser;

    $criteria = Criteria::default()
        ->withFilterGroup(FilterGroup::create()
            ->addFilterNotIn('country', ['es', 'fr']));
    $result = $parser->applyCriteria(UserDatabaseSource::query(), $criteria);

    expect($result['builder']->toRawSql())
        ->toBe('select * from "users" where "country" not in (\'es\', \'fr\') limit 25 offset 0');
});

test('EloquentCriteriaParser should parse like filter.', function () {
    $parser = new EloquentCriteriaParser;

    $criteria = Criteria::default()
        ->withFilterGroup(FilterGroup::create()
            ->addFilterLike('bio', '%developer%'));
    $result = $parser->applyCriteria(UserDatabaseSource::query(), $criteria);

    expect($result['builder']->toRawSql())
        ->toBe('select * from "users" where "bio" like \'%developer%\' limit 25 offset 0');
});

test('EloquentCriteriaParser should parse not like filter.', function () {
    $parser = new EloquentCriteriaParser;

    $criteria = Criteria::default()
        ->withFilterGroup(FilterGroup::create()
            ->addFilterNotLike('bio', '%developer%'));
    $result = $parser->applyCriteria(UserDatabaseSource::query(), $criteria);

    expect($result['builder']->toRawSql())
        ->toBe('select * from "users" where "bio" not like \'%developer%\' limit 25 offset 0');
});

test('EloquentCriteriaParser should parse contains (to like) filter.', function () {
    $parser = new EloquentCriteriaParser;

    $criteria = Criteria::default()
        ->withFilterGroup(FilterGroup::create()
            ->addFilterContains('bio', '%developer%'));
    $result = $parser->applyCriteria(UserDatabaseSource::query(), $criteria);

    expect($result['builder']->toRawSql())
        ->toBe('select * from "users" where "bio" like \'%developer%\' limit 25 offset 0');
});

test('EloquentCriteriaParser should parse not contains (to not like) filter.', function () {
    $parser = new EloquentCriteriaParser;

    $criteria = Criteria::default()
        ->withFilterGroup(FilterGroup::create()
            ->addFilterNotContains('bio', '%developer%'));
    $result = $parser->applyCriteria(UserDatabaseSource::query(), $criteria);

    expect($result['builder']->toRawSql())
        ->toBe('select * from "users" where "bio" not like \'%developer%\' limit 25 offset 0');
});

test('EloquentCriteriaParser should parse domain mapped attributes.', function () {
    $parser = new EloquentCriteriaParser([
        'name' => 'first_name',
        'surname' => 'last_name',
    ]);

    $criteria = Criteria::default()
        ->withFilterGroup(FilterGroup::create()
            ->addFilterEqual('name', 'Vincent'));
    $result = $parser->applyCriteria(UserDatabaseSource::query(), $criteria);

    expect($result['builder']->toRawSql())
        ->toBe('select * from "users" where "first_name" = \'Vincent\' limit 25 offset 0');
});

test('EloquentCriteriaParser should parse ASC ordering.', function () {
    $parser = new EloquentCriteriaParser;

    $criteria = Criteria::default()
        ->withOrderType('asc')
        ->withOrderBy('first_name');
    $result = $parser->applyCriteria(UserDatabaseSource::query(), $criteria);

    expect($result['builder']->toRawSql())
        ->toBe('select * from "users" order by "first_name" asc limit 25 offset 0');
});

test('EloquentCriteriaParser should parse DESC ordering.', function () {
    $parser = new EloquentCriteriaParser;

    $criteria = Criteria::default()
        ->withOrderType('desc')
        ->withOrderBy('last_name');
    $result = $parser->applyCriteria(UserDatabaseSource::query(), $criteria);

    expect($result['builder']->toRawSql())
        ->toBe('select * from "users" order by "last_name" desc limit 25 offset 0');
});

test('EloquentCriteriaParser should parse RANDOM ordering.', function () {
    $parser = new EloquentCriteriaParser;

    $criteria = Criteria::default()
        ->withOrderType('random')
        ->withOrderBy('first_name');
    $result = $parser->applyCriteria(UserDatabaseSource::query(), $criteria);

    expect($result['builder']->toRawSql())
        ->toBe('select * from "users" order by RANDOM() limit 25 offset 0');
});

test('EloquentCriteriaParser should parse 2 or more filters using AND operator.', function () {
    $parser = new EloquentCriteriaParser;

    $criteria = Criteria::default()
        ->withFilterGroup(FilterGroup::create()
            ->addFilterEqual('first_name', 'Vincent')
            ->addFilterEqual('last_name', 'Vega'));
    $result = $parser->applyCriteria(UserDatabaseSource::query(), $criteria);

    expect($result['builder']->toRawSql())
        ->toBe('select * from "users" where "first_name" = \'Vincent\' and "last_name" = \'Vega\' limit 25 offset 0');
});

test('EloquentCriteriaParser should parse 2 or more filters groups using OR operator.', function () {
    $parser = new EloquentCriteriaParser;

    $criteria = Criteria::default()
        ->withFilterGroup(FilterGroup::create()
            ->addFilterEqual('first_name', 'Vincent'))
        ->withFilterGroup(FilterGroup::create()
            ->addFilterEqual('last_name', 'Winnfield'));
    $result = $parser->applyCriteria(UserDatabaseSource::query(), $criteria);

    expect($result['builder']->toRawSql())
        ->toBe('select * from "users" where "first_name" = \'Vincent\' or ("last_name" = \'Winnfield\') limit 25 offset 0');
});

test('EloquentCriteriaParser should ignore Page object if limit is 0.', function () {
    $parser = new EloquentCriteriaParser;

    $criteria = Criteria::default()
        ->withPageLimit(0);
    $result = $parser->applyCriteria(UserDatabaseSource::query(), $criteria);

    expect($result['builder']->toRawSql())
        ->toBe('select * from "users"');
});

test('EloquentCriteriaParser should return array with builder, total, and page.', function () {
    $parser = new EloquentCriteriaParser;

    $criteria = Criteria::default()
        ->withPageNumber(2, 25);
    $result = $parser->applyCriteria(UserDatabaseSource::query(), $criteria);

    expect($result)->toBeArray()
        ->toHaveKeys(['builder', 'total', 'page', 'currentPage'])
        ->and($result['page'])->toBeInstanceOf(Page::class)
        ->and($result['total'])->toBeInt();
});

test('EloquentCriteriaParser should calculate currentPage correctly.', function () {
    $parser = new EloquentCriteriaParser;

    // Page 1
    $criteria = Criteria::default()->withPageNumber(1, 10);
    $result = $parser->applyCriteria(UserDatabaseSource::query(), $criteria);
    expect($result['currentPage'])->toBe(1);

    // Page 2
    $criteria = Criteria::default()->withPageNumber(2, 10);
    $result = $parser->applyCriteria(UserDatabaseSource::query(), $criteria);
    expect($result['currentPage'])->toBe(2);

    // Page 5
    $criteria = Criteria::default()->withPageNumber(5, 25);
    $result = $parser->applyCriteria(UserDatabaseSource::query(), $criteria);
    expect($result['currentPage'])->toBe(5);

    // Edge case: limit 0 should return page 1
    $criteria = Criteria::default()->withPageLimit(0);
    $result = $parser->applyCriteria(UserDatabaseSource::query(), $criteria);
    expect($result['currentPage'])->toBe(1);
});

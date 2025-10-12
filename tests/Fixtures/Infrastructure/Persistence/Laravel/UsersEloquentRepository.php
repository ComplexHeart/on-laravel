<?php

declare(strict_types=1);

namespace ComplexHeart\Tests\Fixtures\Infrastructure\Persistence\Laravel;

use ComplexHeart\Domain\Criteria\Contracts\PaginatedResult;
use ComplexHeart\Domain\Criteria\Criteria;
use ComplexHeart\Infrastructure\Laravel\Pagination\PaginatedCollection;
use ComplexHeart\Infrastructure\Laravel\Persistence\Contracts\IlluminateCriteriaParser;
use ComplexHeart\Infrastructure\Laravel\Persistence\EloquentCriteriaParser;
use ComplexHeart\Tests\Fixtures\Domain\Contracts\UserRepository;
use ComplexHeart\Tests\Fixtures\Domain\User;
use ComplexHeart\Tests\Fixtures\Infrastructure\Persistence\Laravel\Sources\UserDatabaseSource as Table;

/**
 * Class UsersEloquentRepository
 *
 * @author Unay Santisteban <usantisteban@othercode.io>
 */
class UsersEloquentRepository implements UserRepository
{
    private IlluminateCriteriaParser $criteriaParser;

    public function __construct()
    {
        $this->criteriaParser = new EloquentCriteriaParser([
            'name' => 'first_name',
            'surname' => 'last_name',
        ]);
    }

    /**
     * @return PaginatedResult<User>
     */
    public function match(Criteria $criteria): PaginatedResult
    {
        $result = $this->criteriaParser->applyCriteria(Table::query(), $criteria);

        $items = $result['builder']
            ->get()
            ->map(fn (Table $row) => User::fromSource($row))
            ->values()
            ->toArray();

        return PaginatedCollection::paginate(
            items: $items,
            total: $result['total'],
            perPage: $result['page']->limit(),
            currentPage: $result['currentPage'],
        );
    }
}

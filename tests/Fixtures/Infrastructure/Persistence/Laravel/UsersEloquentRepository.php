<?php

declare(strict_types=1);

namespace ComplexHeart\Tests\Fixtures\Infrastructure\Persistence\Laravel;

use ComplexHeart\Domain\Criteria\Criteria;
use ComplexHeart\Infrastructure\Laravel\Persistence\EloquentCriteriaParser;
use ComplexHeart\Infrastructure\Laravel\Persistence\Contracts\IlluminateCriteriaParser;
use ComplexHeart\Tests\Fixtures\Domain\Contracts\UserRepository;
use ComplexHeart\Tests\Fixtures\Domain\User;
use ComplexHeart\Tests\Fixtures\Infrastructure\Persistence\Laravel\Sources\UserDatabaseSource as Table;
use Illuminate\Support\Collection;

/**
 * Class UsersEloquentRepository
 *
 * @author Unay Santisteban <usantisteban@othercode.io>
 * @package ComplexHeart\Tests\Fixtures\Infrastructure\Persistence\Laravel
 */
class UsersEloquentRepository implements UserRepository
{
    private IlluminateCriteriaParser $criteriaParser;

    public function __construct()
    {
        $this->criteriaParser = new EloquentCriteriaParser([
            'name' => 'first_name',
            'surname' => 'last_name'
        ]);
    }

    /**
     * @param  Criteria  $criteria
     * @return Collection<User>
     */
    public function match(Criteria $criteria): Collection
    {
        return $this->criteriaParser
            ->applyCriteria(Table::query(), $criteria)
            ->get()
            ->map(fn(Table $row) => User::fromSource($row))
            ->values();
    }
}

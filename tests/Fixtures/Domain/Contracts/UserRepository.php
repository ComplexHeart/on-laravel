<?php

declare(strict_types=1);

namespace ComplexHeart\Tests\Fixtures\Domain\Contracts;

use ComplexHeart\Domain\Criteria\Contracts\PaginatedResult;
use ComplexHeart\Domain\Criteria\Criteria;
use ComplexHeart\Tests\Fixtures\Domain\User;

/**
 * Interface UserRepository
 *
 * @author Unay Santisteban <usantisteban@othercode.io>
 */
interface UserRepository
{
    /**
     * @return PaginatedResult<User>
     */
    public function match(Criteria $criteria): PaginatedResult;
}

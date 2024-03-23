<?php

declare(strict_types=1);

namespace ComplexHeart\Tests\Fixtures\Domain\Contracts;

use ComplexHeart\Domain\Criteria\Criteria;
use ComplexHeart\Tests\Fixtures\Domain\User;
use Illuminate\Support\Collection;

/**
 * Interface UserRepository
 *
 * @author Unay Santisteban <usantisteban@othercode.io>
 * @package ComplexHeart\Tests\Fixtures\Domain\Contracts
 */
interface UserRepository
{
    /**
     * @param  Criteria  $criteria
     * @return Collection<User>
     */
    public function match(Criteria $criteria): Collection;
}

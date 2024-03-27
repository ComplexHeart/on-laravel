<?php

declare(strict_types=1);

namespace ComplexHeart\Infrastructure\Laravel\Persistence\Contracts;

use ComplexHeart\Domain\Criteria\Criteria;
use Illuminate\Contracts\Database\Query\Builder;

/**
 * Interface IlluminateCriteriaParser
 *
 * @author Unay Santisteban <usantisteban@othercode.io>
 * @package ComplexHeart\Infrastructure\Laravel\Persistence\Contracts
 */
interface IlluminateCriteriaParser
{
    /**
     * Apply a criteria into the given QueryBuilder.
     *
     * @param  Builder  $builder
     * @param  Criteria  $criteria
     * @return Builder
     */
    public function applyCriteria(Builder $builder, Criteria $criteria): Builder;
}

<?php

declare(strict_types=1);

namespace ComplexHeart\Infrastructure\Laravel\Persistence\Contracts;

use ComplexHeart\Domain\Criteria\Criteria;
use ComplexHeart\Domain\Criteria\Page;
use Illuminate\Contracts\Database\Query\Builder;

/**
 * Interface IlluminateCriteriaParser
 *
 * @author Unay Santisteban <usantisteban@othercode.io>
 */
interface IlluminateCriteriaParser
{
    /**
     * Apply a criteria into the given QueryBuilder.
     *
     * Returns an array containing:
     * - 'builder': Query builder with filters, ordering, and pagination applied
     * - 'total': Total count before pagination
     * - 'page': Page value object from criteria
     * - 'currentPage': Current page number (1-indexed)
     *
     * @return array{builder: Builder, total: int, page: Page, currentPage: int}
     */
    public function applyCriteria(Builder $builder, Criteria $criteria): array;
}

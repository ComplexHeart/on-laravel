<?php

declare(strict_types=1);

namespace ComplexHeart\Infrastructure\Laravel\Pagination;

use ComplexHeart\Domain\Criteria\Contracts\PaginatedResult;
use Illuminate\Support\Collection;

/**
 * Class PaginatedCollection
 *
 * @template TKey of array-key
 * @template TValue
 *
 * @extends Collection<TKey, TValue>
 *
 * @author Unay Santisteban <usantisteban@othercode.io>
 */
class PaginatedCollection extends Collection implements PaginatedResult
{
    /**
     * PaginatedCollection constructor.
     *
     * @param  iterable<TKey, TValue>  $items
     */
    public function __construct(
        iterable $items = [],
        private readonly int $total = 0,
        private readonly int $perPage = 0,
        private readonly int $currentPage = 1,
    ) {
        parent::__construct($items);
    }

    /**
     * Create a new PaginatedCollection instance.
     *
     * @param  array<TKey, TValue>  $items
     * @return self<TKey, TValue>
     */
    public static function paginate(
        array $items,
        int $total,
        int $perPage,
        int $currentPage
    ): self {
        return new self($items, $total, $perPage, $currentPage);
    }

    /**
     * Get the items for the current page
     *
     * @return array<int, mixed>
     */
    public function items(): array
    {
        return $this->all();
    }

    /**
     * Get total number of items across all pages
     */
    public function total(): int
    {
        return $this->total;
    }

    /**
     * Get items per page
     */
    public function perPage(): int
    {
        return $this->perPage;
    }

    /**
     * Get current page number (1-indexed)
     */
    public function currentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * Get last page number
     */
    public function lastPage(): int
    {
        if ($this->perPage === 0) {
            return 1;
        }

        return (int) ceil($this->total / $this->perPage);
    }

    /**
     * Check if there are more pages after the current one
     */
    public function hasMorePages(): bool
    {
        return $this->currentPage < $this->lastPage();
    }

    /**
     * Check if the current page is empty
     */
    public function isEmpty(): bool
    {
        return parent::isEmpty();
    }

    /**
     * Check if the current page is not empty
     */
    public function isNotEmpty(): bool
    {
        return parent::isNotEmpty();
    }
}

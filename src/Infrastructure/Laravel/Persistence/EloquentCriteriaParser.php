<?php

declare(strict_types=1);

namespace ComplexHeart\Infrastructure\Laravel\Persistence;

use ComplexHeart\Domain\Criteria\Criteria;
use ComplexHeart\Domain\Criteria\Filter;
use ComplexHeart\Domain\Criteria\FilterGroup;
use ComplexHeart\Domain\Criteria\Operator;
use ComplexHeart\Domain\Criteria\Order;
use ComplexHeart\Domain\Criteria\Page;
use ComplexHeart\Infrastructure\Laravel\Persistence\Contracts\IlluminateCriteriaParser;
use Illuminate\Contracts\Database\Query\Builder;

/**
 * Class EloquentCriteriaParser
 *
 * @author Unay Santisteban <usantisteban@othercode.io>
 * @package ComplexHeart\Infrastructure\Laravel\Persistence
 */
class EloquentCriteriaParser implements IlluminateCriteriaParser
{
    /**
     * DefaultCriteriaParser constructor.
     *
     * @param  array<string, string>  $filterAttributes
     */
    public function __construct(private readonly array $filterAttributes = [])
    {
    }

    /**
     * Returns the persistence attribute name based on given
     * domain attribute name if exists.
     *
     * The key is the domain name attribute nad the value is
     * the name of the attribute in the persistence system.
     *
     *  'domain-attribute' => 'persistence-attribute'
     *
     * For example:
     *
     * $this->filterAttributes = [
     *  'owner' => 'user_id',
     *  'title' => 'title',
     *  'type' => 'type',
     *  'opensource' => 'is_opensource',
     * ]
     *
     * $this->filterAttribute('owner') returns 'user_id'
     *
     * @param  string  $fieldAttribute
     * @return string
     */
    private function filterAttribute(string $fieldAttribute): string
    {
        return $this->filterAttributes[$fieldAttribute] ?? $fieldAttribute;
    }

    /**
     * Apply a criteria into the given Query Builder.
     *
     * @param  Builder  $builder
     * @param  Criteria  $criteria
     * @return Builder
     */
    public function applyCriteria(Builder $builder, Criteria $criteria): Builder
    {
        $builder = $this->applyFilterGroups($builder, $criteria->groups());
        $builder = $this->applyOrdering($builder, $criteria->order());

        return $this->applyPage($builder, $criteria->page());
    }

    /**
     * Apply the given list of filter groups into the given QueryBuilder.
     *
     * @param  Builder  $builder
     * @param  array<int, FilterGroup>  $groups
     * @return Builder
     */
    private function applyFilterGroups(Builder $builder, array $groups): Builder
    {
        foreach ($groups as $index => $group) {
            $builder = $index === 0
                ? $this->applyFilters($builder, $group)
                : $builder->orWhere(function (Builder $query) use ($group) {
                    $this->applyFilters($query, $group);
                });
        }

        return $builder;
    }

    /**
     * Apply a set of filters into the given QueryBuilder.
     *
     * @param  Builder  $builder
     * @param  FilterGroup<Filter>  $filters
     * @return Builder
     */
    private function applyFilters(Builder $builder, FilterGroup $filters): Builder
    {
        foreach ($filters as $filter) {
            $builder = $this->applyFilter($builder, $filter);
        }

        return $builder;
    }

    /**
     * Apply a filter into the given QueryBuilder.
     *
     * @param  Builder  $builder
     * @param  Filter  $filter
     * @return Builder
     */
    private function applyFilter(Builder $builder, Filter $filter): Builder
    {
        $field = $this->filterAttribute($filter->field());

        switch ($filter->operator()) {
            case Operator::IN:
                $builder->whereIn($field, $filter->value());
                break;
            case Operator::NOT_IN:
                $builder->whereNotIn($field, $filter->value());
                break;
            // redirect the contains operator to like.
            case Operator::CONTAINS:
                $builder->where($field, Operator::LIKE->value, $filter->value());
                break;
            // redirect the not contains operator to not like.
            case Operator::NOT_CONTAINS:
                $builder->where($field, Operator::NOT_LIKE->value, $filter->value());
                break;
            default:
                $builder->where($field, $filter->operator()->value, $filter->value());
        }

        return $builder;
    }

    /**
     * Apply the ordering settings into the given QueryBuilder.
     *
     * @param  Builder  $builder
     * @param  Order  $ordering
     * @return Builder
     */
    private function applyOrdering(Builder $builder, Order $ordering): Builder
    {
        if (!$ordering->isNone()) {
            $filterAttribute = $this->filterAttribute($ordering->by());

            $builder = ($ordering->isRandom())
                ? $builder->inRandomOrder()
                : $builder->orderBy($filterAttribute, $ordering->type()->value);
        }

        return $builder;
    }

    /**
     * Apply the page settings (limit and offset) into the given QueryBuilder.
     *
     * @param  Builder  $builder
     * @param  Page  $page
     * @return Builder
     */
    private function applyPage(Builder $builder, Page $page): Builder
    {
        if ($page->limit() > 0) {
            $builder = $builder->limit($page->limit());
            $builder = $builder->offset($page->offset());
        }

        return $builder;
    }
}

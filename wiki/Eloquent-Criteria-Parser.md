# Eloquent Criteria Parser

The Eloquent Criteria Parser translates domain Criteria objects into Eloquent query builders with pagination support.

## Basic Usage

Instantiate the parser and pass a Criteria instance along with an `EloquentQueryBuilder`:

```php
$parser = new EloquentCriteriaParser();
$result = $parser->applyCriteria(User::query(), $criteria);
```

The `applyCriteria()` method returns an array containing:

- `builder`: EloquentQueryBuilder with filters, ordering, and pagination applied
- `total`: Total count of records before pagination
- `page`: Page value object from the criteria
- `currentPage`: Current page number (1-indexed)

```php
$items = $result['builder']->get();
$total = $result['total'];
$currentPage = $result['currentPage'];
```

## Attribute Mapping

Map domain attributes to database columns by passing an array to the constructor:

```php
$parser = new EloquentCriteriaParser([
    'domain-attribute' => 'database-attribute',
]);
```

### Example

Given this users table:

```php
$this->builder->create('users', function (Blueprint $table) {
    $table->uuid('id');
    $table->string('first_name');
    $table->string('last_name');
    $table->string('email')->unique();
    $table->string('bio')->nullable();
    $table->timestamps();
});
```

Use `name` and `surname` in your domain instead of `first_name` and `last_name`:

```php
$parser = new EloquentCriteriaParser([
    'name' => 'first_name',
    'surname' => 'last_name'
]);
```

## Repository Implementation with PaginatedCollection

The recommended pattern is to use `PaginatedCollection` in your repositories:

```php
use ComplexHeart\Domain\Criteria\Contracts\PaginatedResult;
use ComplexHeart\Infrastructure\Laravel\Pagination\PaginatedCollection;

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

    public function match(Criteria $criteria): PaginatedResult
    {
        $result = $this->criteriaParser->applyCriteria(User::query(), $criteria);

        $items = $result['builder']
            ->get()
            ->map(fn($row) => UserEntity::fromSource($row))
            ->toArray();

        return PaginatedCollection::paginate(
            items: $items,
            total: $result['total'],
            perPage: $result['page']->limit(),
            currentPage: $result['currentPage'],
        );
    }
}
```

## PaginatedCollection

`PaginatedCollection` extends Laravel's `Collection` and implements the `PaginatedResult` interface from php-criteria.

### Collection Methods Support

You can use all Laravel Collection methods:

```php
$result = $repository->match($criteria);

// Standard Collection methods
$names = $result->map(fn($user) => $user->name)->toArray();
$emails = $result->pluck('email')->toArray();
$filtered = $result->filter(fn($user) => $user->isActive);
```

### Pagination Metadata

Access pagination information through these methods:

```php
$result->items();          // Get current page items as array
$result->total();          // Total records across all pages
$result->perPage();        // Items per page
$result->currentPage();    // Current page number (1-indexed)
$result->lastPage();       // Last page number
$result->hasMorePages();   // Check if more pages exist
$result->isEmpty();        // Check if current page is empty
$result->isNotEmpty();     // Check if current page has items
$result->count();          // Count items on current page
```

### Example Usage

```php
$criteria = Criteria::default()
    ->withFilterGroup(FilterGroup::create()
        ->addFilterEqual('name', 'Vincent'))
    ->withPageNumber(1, 25);

$result = $repository->match($criteria);

if ($result->isNotEmpty()) {
    echo "Page {$result->currentPage()} of {$result->lastPage()}";
    echo "Showing {$result->count()} of {$result->total()} users";

    foreach ($result as $user) {
        echo $user->name;
    }
}
```

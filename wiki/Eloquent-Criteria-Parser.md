# Eloquent Criteria Parser

Using the Eloquent Criteria Parser is quite simple. Just instantiate the class, next, pass a Criteria instance to
the `applyCriteria()` method along with a `EloquentQueryBuilder` instance:

```php
$parser = new EloquentCriteriaParser();
$query = $parser->applyCriteria(User::query(), $criteria);
```

The returned `EloquentQueryBuilder` has the criteria applied. You just need to call the `get` method to fetch the data
from the database.

```php
$users = $query->get();
```

Alternatively, you can pass an array of strings to map the attributes between the domain and the database.

```php
$parser = new EloquentCriteriaParser([
    'domain-attribute' => 'database-attribute',
]);
```

For example,
given the following table:

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

You may use the following configuration to use `name` and `surname` instead of `first_name` and `last_name`:

```php
$parser = new EloquentCriteriaParser([
    'name' => 'first_name',
    'surname' => 'last_name'
]);
```

A criteria search will be something like this:

```php
$criteria = Criteria::default()
    ->withFilterGroup(FilterGroup::create()
        ->addFilterEqual('name', 'Vicent'));

$builder = User::query();

$parser = new EloquentCriteriaParser();
$users = $parser
    ->applyCriteria($builder, $criteria)
    ->get();
```

This is useful to expose different attributes from different interfaces as HTTP, or CLI.  
<?php

declare(strict_types=1);

namespace ComplexHeart\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;

class ModelWithNew extends Model
{
    public bool $createdViaNew = false;

    public string $name = '';

    protected $guarded = [];

    public static function new(string $name = ''): static
    {
        $instance = new static();
        $instance->createdViaNew = true;
        $instance->name = $name;

        return $instance;
    }
}

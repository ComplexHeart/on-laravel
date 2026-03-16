<?php

declare(strict_types=1);

namespace ComplexHeart\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;

class ModelWithoutNew extends Model
{
    public bool $createdViaNew = false;

    protected $guarded = [];
}

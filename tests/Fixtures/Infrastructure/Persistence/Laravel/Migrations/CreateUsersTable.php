<?php

declare(strict_types=1);

namespace ComplexHeart\Tests\Fixtures\Infrastructure\Persistence\Laravel\Migrations;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

/**
 * Class CreateUsersTable
 *
 * @author Unay Santisteban <usantisteban@othercode.io>
 */
class CreateUsersTable
{
    public function __construct(private readonly Builder $builder) {}

    public function up(): void
    {
        $this->builder->create('users', function (Blueprint $table) {
            $table->uuid('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('bio')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        $this->builder->drop('users');
    }
}

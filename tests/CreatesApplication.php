<?php

declare(strict_types=1);

namespace ComplexHeart\Tests;

use ComplexHeart\Tests\Fixtures\Infrastructure\Persistence\Laravel\Migrations\CreateUsersTable;
use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * Trait CreatesApplication
 *
 * @author Unay Santisteban <usantisteban@othercode.io>
 */
trait CreatesApplication
{
    private function bootEloquent(): void
    {
        $capsule = new Capsule();
        $capsule->addConnection([
            'driver' => 'sqlite',
            'database' => ':memory:',
        ]);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        $migrations = [
            CreateUsersTable::class,
        ];

        foreach ($migrations as $migration) {
            (new $migration($capsule->schema()))->up();
        }
    }

    /**
     * Currently this method only boots the database and
     * eloquent system. In future iterations this may really
     * boot a complete Laravel application instance.
     */
    public function createApplication(): void
    {
        $this->bootEloquent();
    }
}

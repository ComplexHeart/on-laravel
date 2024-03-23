<?php

declare(strict_types=1);

namespace ComplexHeart\Tests\Fixtures\Infrastructure\Persistence\Laravel\Sources;

use ComplexHeart\Tests\Fixtures\Domain\Contracts\UserSource;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User
 *
 * @property string $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $bio
 *
 * @author Unay Santisteban <usantisteban@othercode.io>
 * @package ComplexHeart\Tests\Fixtures\Infrastructure\Persistence
 */
class UserDatabaseSource extends Model implements UserSource
{
    use HasUuids;

    protected $table = 'users';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'bio',
    ];

    public function userIdentifier(): string
    {
        return $this->id;
    }

    public function userName(): string
    {
        return $this->first_name;
    }

    public function userSurname(): string
    {
        return $this->last_name;
    }

    public function userEmail(): string
    {
        return $this->email;
    }

    public function userBio(): string
    {
        return $this->bio;
    }
}

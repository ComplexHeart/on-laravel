<?php

declare(strict_types=1);

namespace ComplexHeart\Tests\Fixtures\Domain\Contracts;

/**
 * Interface UserSource
 *
 * @author Unay Santisteban <usantisteban@othercode.io>
 */
interface UserSource
{
    public function userIdentifier(): string;

    public function userName(): string;

    public function userSurname(): string;

    public function userEmail(): string;

    public function userBio(): string;
}

<?php

declare(strict_types=1);

namespace ComplexHeart\Tests\Fixtures\Domain;

use ComplexHeart\Domain\Contracts\Model\Aggregate;
use ComplexHeart\Domain\Contracts\Model\Identifier;
use ComplexHeart\Domain\Contracts\Events\EventBus;
use ComplexHeart\Domain\Model\ValueObjects\UUIDValue as Id;
use ComplexHeart\Tests\Fixtures\Domain\Contracts\UserSource;

/**
 * Class User
 *
 * @author Unay Santisteban <usantisteban@othercode.io>
 */
class User implements Aggregate
{
    public function __construct(
        public readonly Id $id,
        public string $name,
        public string $surname,
        public string $email,
        public string $bio,
    ) {
    }

    public static function fromSource(UserSource $source): self
    {
        return new self(
            id: new Id($source->userIdentifier()),
            name: $source->userName(),
            surname: $source->userSurname(),
            email: $source->userEmail(),
            bio: $source->userBio()
        );
    }

    public function id(): Identifier
    {
        return $this->id;
    }

    public function publishDomainEvents(EventBus $eventBus): void
    {
        // TODO: Implement publishDomainEvents() method.
    }
}

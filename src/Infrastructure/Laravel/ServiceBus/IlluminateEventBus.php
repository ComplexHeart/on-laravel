<?php

declare(strict_types=1);

namespace ComplexHeart\Infrastructure\Laravel\ServiceBus;

use ComplexHeart\Domain\Contracts\Events\Event;
use ComplexHeart\Domain\Contracts\Events\EventBus;

/**
 * Class IlluminateEventBus
 *
 * Bridges ComplexHeart's EventBus contract to Laravel's event dispatcher.
 * Domain events are dispatched through Laravel's event() helper,
 * which routes listeners by FQCN (fully qualified class name).
 *
 * @author Unay Santisteban <usantisteban@othercode.io>
 */
class IlluminateEventBus implements EventBus
{
    /**
     * Publish domain events through Laravel's event dispatcher.
     */
    public function publish(Event ...$events): void
    {
        foreach ($events as $event) {
            event($event);
        }
    }
}

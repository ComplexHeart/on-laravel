<?php

declare(strict_types=1);

namespace ComplexHeart\Tests\Unit;

use ComplexHeart\Domain\Contracts\Events\Event;
use ComplexHeart\Domain\Contracts\Events\EventBus;
use ComplexHeart\Infrastructure\Laravel\ServiceBus\IlluminateEventBus;

it('should implement the EventBus contract', function () {
    $bus = new IlluminateEventBus();

    expect($bus)->toBeInstanceOf(EventBus::class);
});

it('should dispatch events through laravel event helper', function () {
    $event = \Mockery::mock(Event::class);

    $dispatched = [];

    // Override Laravel's event() helper behavior by registering a
    // custom event dispatcher in the container.
    $dispatcher = \Mockery::mock(\Illuminate\Contracts\Events\Dispatcher::class);
    $dispatcher->shouldReceive('dispatch')
        ->twice()
        ->andReturnUsing(function ($event) use (&$dispatched) {
            $dispatched[] = $event;
        });

    $app = \Illuminate\Container\Container::getInstance();
    $app->instance('events', $dispatcher);

    $event1 = \Mockery::mock(Event::class);
    $event2 = \Mockery::mock(Event::class);

    $bus = new IlluminateEventBus();
    $bus->publish($event1, $event2);

    expect($dispatched)->toHaveCount(2)
        ->and($dispatched[0])->toBe($event1)
        ->and($dispatched[1])->toBe($event2);
});

it('should handle empty events gracefully', function () {
    $dispatcher = \Mockery::mock(\Illuminate\Contracts\Events\Dispatcher::class);
    $dispatcher->shouldNotReceive('dispatch');

    $app = \Illuminate\Container\Container::getInstance();
    $app->instance('events', $dispatcher);

    $bus = new IlluminateEventBus();
    $bus->publish();

    expect(true)->toBeTrue();
});

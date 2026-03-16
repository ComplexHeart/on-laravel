# Illuminate Event Bus

The `IlluminateEventBus` bridges ComplexHeart's `EventBus` contract to Laravel's event dispatcher. Domain events are dispatched through Laravel's `event()` helper, which routes listeners by FQCN (fully qualified class name).

## Basic Usage

Bind the `EventBus` contract to `IlluminateEventBus` in your service provider:

```php
use ComplexHeart\Domain\Contracts\Events\EventBus;
use ComplexHeart\Infrastructure\Laravel\ServiceBus\IlluminateEventBus;

public array $bindings = [
    EventBus::class => IlluminateEventBus::class,
];
```

## Publishing Domain Events

Domain events are collected by aggregates using the `HasDomainEvents` trait and published through the `EventBus` in use cases:

```php
final readonly class PlaceOrder
{
    public function __construct(
        private OrderRepository $orders,
        private EventBus $eventBus,
    ) {
    }

    public function __invoke(string $id, string $customerId, float $total): Order
    {
        $order = Order::place($id, $customerId, $total);
        $this->orders->store($order);
        $order->publishDomainEvents($this->eventBus);
        return $order;
    }
}
```

## Registering Listeners

Laravel listeners receive the full ComplexHeart `Event` object. Register them using the `$events` array in your `BoundedContextServiceProvider`:

```php
protected array $events = [
    OrderPlaced::class => [
        SendOrderConfirmation::class,
        UpdateInventory::class,
    ],
];
```

Or register them manually with `Event::listen()`:

```php
Event::listen(OrderPlaced::class, SendOrderConfirmation::class);
```

### Listener Example

```php
class SendOrderConfirmation implements ShouldQueue
{
    public function handle(OrderPlaced $event): void
    {
        // Access event metadata
        $eventId = $event->eventId();
        $eventName = $event->eventName();     // "order.placed"
        $occurredOn = $event->occurredOn();

        // Access payload directly
        $orderId = $event->orderId;
        $customerId = $event->customerId;
        $total = $event->total;

        // Or as array
        $payload = $event->payload();
    }
}
```

## How It Works

Laravel's event dispatcher routes events by their FQCN (`get_class($event)`). When `IlluminateEventBus::publish()` is called, it passes each domain event to Laravel's `event()` helper, which dispatches it to any registered listeners.

```
Aggregate → registerDomainEvent(OrderPlaced::new(...))
         → publishDomainEvents($eventBus)
         → IlluminateEventBus::publish(OrderPlaced)
         → event(OrderPlaced)  // Laravel's event() helper
         → Laravel dispatches to listeners by FQCN
```

## Queued Listeners

Domain events carry only primitives (strings, integers, floats, arrays), so PHP's native `serialize()` handles queued listeners without issues. There is no need for Laravel's `SerializesModels` trait on domain events.

```php
class UpdateInventory implements ShouldQueue
{
    public string $queue = 'inventory';

    public function handle(OrderPlaced $event): void
    {
        // This runs asynchronously on the 'inventory' queue
    }
}
```

## Broadcasting

Domain events should not implement `ShouldBroadcast` directly — that would couple them to Laravel. Instead, use a bridging listener that creates a Laravel-specific broadcast event:

```php
// Domain listener bridges to Laravel broadcast
class BroadcastOrderPlaced implements ShouldQueue
{
    public function handle(OrderPlaced $event): void
    {
        broadcast(new OrderPlacedBroadcast(
            orderId: $event->orderId,
            total: $event->total,
        ));
    }
}

// Laravel broadcast event (infrastructure)
class OrderPlacedBroadcast implements ShouldBroadcast
{
    public function __construct(
        public readonly string $orderId,
        public readonly float $total,
    ) {
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel("orders.{$this->orderId}")];
    }
}
```

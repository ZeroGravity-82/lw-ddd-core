<?php

declare(strict_types=1);

namespace Lw\DddCore\Domain\Model;

/**
 * @author Nikolay Ryabkov <nikolay.ryabkov@sibers.com>
 */
class EventDispatcher
{
    private array $listeners = [];

    /**
     * Adds the listener to an iterable (array, iterator, or generator) of callables that listen for the event. The
     * listener callable must be type-compatible with the event.
     *
     * @throws \LogicException when the same event listener has already been added for the event class
     */
    public function addListener(string $eventClass, callable $listener): void
    {
        $this->assertValidEventClass($eventClass, 'add');
        if (!isset($this->listeners[$eventClass])) {
            $this->listeners[$eventClass] = [];
        }
        if (\in_array($listener, $this->listeners[$eventClass])) {
            throw new \LogicException(
                \sprintf(
                    'This event listener of type "%s" for event class "%s" already added.',
                    \get_debug_type($listener),
                    $eventClass
                )
            );
        }
        $this->listeners[$eventClass][] = $listener;
    }

    /**
     * Removes the listener from an iterable (array, iterator, or generator) of callables that listen for the event.
     */
    public function removeListener(string $eventClass, callable $listener): void
    {
        $this->assertValidEventClass($eventClass, 'remove');
        if (!isset($this->listeners[$eventClass])) {
            return;
        }
        $key = \array_search($listener, $this->listeners[$eventClass]);
        if ($key !== false) {
            unset($this->listeners[$eventClass][$key]);
            $this->listeners[$eventClass] = \array_values($this->listeners[$eventClass]);
        }
    }

    /**
     * Returns listeners relevant to the event.
     */
    public function getListenersForEvent(AbstractEvent $event): array
    {
        $listenersForEvent = [];
        foreach ($this->listeners as $eventClass => $listeners) {
            if (!$event instanceof $eventClass) {
                continue;
            }
            foreach ($listeners as $listener) {
                $listenersForEvent[] = $listener;
            }
        }

        return $listenersForEvent;
    }

    /**
     * Provides all relevant listeners with the events to process.
     */
    public function dispatch(AbstractEvent ...$events): void
    {
        foreach ($events as $event) {
            foreach ($this->getListenersForEvent($event) as $listener) {
                $listener($event);
            }
        }
    }

    private function assertValidEventClass(string $eventClass, string $actionName): void
    {
        if ($eventClass !== AbstractEvent::class && !\is_subclass_of($eventClass, AbstractEvent::class)) {
            throw new \LogicException(\sprintf(
                'Invalid event class to %s event listener: expected sub-class of "%s", "%s" given.',
                $actionName,
                AbstractEvent::class,
                $eventClass,
            ));
        }
    }
}

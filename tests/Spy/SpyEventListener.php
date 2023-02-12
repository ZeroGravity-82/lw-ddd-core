<?php

declare(strict_types=1);

namespace Spy\Tests;

use Lw\DddCore\Domain\Model\AbstractEvent;
use Lw\DddCore\Domain\Model\EventDispatcher;

/**
 * Spy listener that listens for all events and is used to test that various objects publish events.
 *
 * @author Nikolay Ryabkov <nikolay.ryabkov@sibers.com>
 */
class SpyEventListener
{
    private array $events = [];

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $eventDispatcher->addListener(AbstractEvent::class, $this->getHandleAnyEventCallable());
    }

    public function shiftEvent(): ?AbstractEvent
    {
        return \array_shift($this->events);
    }

    private function getHandleAnyEventCallable(): callable
    {
        return function (AbstractEvent $event) {
            $this->events[] = $event;
        };
    }
}

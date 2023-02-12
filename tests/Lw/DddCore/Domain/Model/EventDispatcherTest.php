<?php

declare(strict_types=1);

namespace Lw\DddCore\Tests\Domain\Model;

use Lw\DddCore\Domain\Model\AbstractEvent;
use Lw\DddCore\Domain\Model\EventDispatcher;
use PHPUnit\Framework\TestCase;
use Spy\Tests\SpyEventListener;

/**
 * @author Nikolay Ryabkov <nikolay.ryabkov@sibers.com>
 */
class EventDispatcherTest extends TestCase
{
    private EventDispatcher  $eventDispatcher;

    public function setUp(): void
    {
        $this->eventDispatcher = new EventDispatcher();
    }

    public function testItAddsEventListeners(): void
    {
        $eventA     = new TestEventA();
        $callableA1 = function () {};
        $callableA2 = function () {};
        $callableA3 = function () {};
        $eventB     = new TestEventB();
        $callableB1 = function () {};
        $this->eventDispatcher->addListener($eventA::class, $callableA1);
        $this->eventDispatcher->addListener($eventA::class, $callableA2);
        $this->eventDispatcher->addListener($eventA::class, $callableA3);
        $this->eventDispatcher->addListener($eventB::class, $callableB1);

        $this->assertIsArray($this->eventDispatcher->getListenersForEvent($eventA));
        $this->assertCount(3, $this->eventDispatcher->getListenersForEvent($eventA));
        $this->assertSame($callableA1, $this->eventDispatcher->getListenersForEvent($eventA)[0]);
        $this->assertSame($callableA2, $this->eventDispatcher->getListenersForEvent($eventA)[1]);
        $this->assertSame($callableA3, $this->eventDispatcher->getListenersForEvent($eventA)[2]);

        $this->assertIsArray($this->eventDispatcher->getListenersForEvent($eventB));
        $this->assertCount(1, $this->eventDispatcher->getListenersForEvent($eventB));
        $this->assertSame($callableB1, $this->eventDispatcher->getListenersForEvent($eventB)[0]);
    }

    public function testItFailsToAddTheSameEventListenerTwice(): void
    {
        $event    = new TestEventA();
        $callable = function () {};
        $this->eventDispatcher->addListener($event::class, $callable);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(
            \sprintf('This event listener of type "Closure" for event class "%s" already added.', $event::class)
        );
        $this->eventDispatcher->addListener($event::class, $callable);
    }

    public function testItFailsToAddTheSameEventListenerTwiceForSuperEvent(): void
    {
        $callable = function () {};
        $this->eventDispatcher->addListener(AbstractEvent::class, $callable);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(
            \sprintf('This event listener of type "Closure" for event class "%s" already added.', AbstractEvent::class)
        );
        $this->eventDispatcher->addListener(AbstractEvent::class, $callable);
    }

    public function testItFailsToAddEventListenerForNonEventClass(): void
    {
        $callable = function () {};
        $this->expectExceptionForNonEventClass('add');
        $this->eventDispatcher->addListener(\stdClass::class, $callable);
    }

    public function testItReturnsListenersForChildEvent(): void
    {
        $parentEvent    = new TestEventE();
        $childEvent     = new TestChildEventE();
        $callableParent = function () {};
        $callableChild  = function () {};
        $this->eventDispatcher->addListener($parentEvent::class, $callableParent);
        $this->eventDispatcher->addListener($childEvent::class, $callableChild);
        $this->assertIsArray($this->eventDispatcher->getListenersForEvent($childEvent));
        $this->assertCount(2, $this->eventDispatcher->getListenersForEvent($childEvent));
        $this->assertSame($callableParent, $this->eventDispatcher->getListenersForEvent($childEvent)[0]);
        $this->assertSame($callableChild, $this->eventDispatcher->getListenersForEvent($childEvent)[1]);

        $this->assertIsArray($this->eventDispatcher->getListenersForEvent($parentEvent));
        $this->assertIsArray($this->eventDispatcher->getListenersForEvent($parentEvent));
        $this->assertCount(1, $this->eventDispatcher->getListenersForEvent($parentEvent));
        $this->assertSame($callableParent, $this->eventDispatcher->getListenersForEvent($parentEvent)[0]);
    }

    public function testItRemovesListeners(): void
    {
        $eventA     = new TestEventA();
        $callableA1 = function () {};
        $callableA2 = function () {};
        $callableA3 = function () {};
        $eventB     = new TestEventB();
        $callableB1 = function () {};
        $callableB2 = function () {};
        $this->eventDispatcher->addListener($eventA::class, $callableA1);
        $this->eventDispatcher->addListener($eventA::class, $callableA2);
        $this->eventDispatcher->addListener($eventA::class, $callableA3);
        $this->eventDispatcher->addListener($eventB::class, $callableB1);
        $this->eventDispatcher->addListener($eventB::class, $callableB2);
        $this->eventDispatcher->removeListener($eventA::class, $callableA2);

        $this->assertIsArray($this->eventDispatcher->getListenersForEvent($eventA));
        $this->assertCount(2, $this->eventDispatcher->getListenersForEvent($eventA));
        $this->assertSame($callableA1, $this->eventDispatcher->getListenersForEvent($eventA)[0]);
        $this->assertSame($callableA3, $this->eventDispatcher->getListenersForEvent($eventA)[1]);

        $this->assertIsArray($this->eventDispatcher->getListenersForEvent($eventB));
        $this->assertCount(2, $this->eventDispatcher->getListenersForEvent($eventB));
        $this->assertSame($callableB1, $this->eventDispatcher->getListenersForEvent($eventB)[0]);
        $this->assertSame($callableB2, $this->eventDispatcher->getListenersForEvent($eventB)[1]);
    }

    public function testItFailsToRemoveEventListenerForNonEventClass(): void
    {
        $callable = function () {};
        $this->expectExceptionForNonEventClass('remove');
        $this->eventDispatcher->removeListener(\stdClass::class, $callable);
    }

    public function testItCallsListenersWhenDispatchesEvent(): void
    {
        $spyEventListener = new SpyEventListener($this->eventDispatcher);

        $eventA = new TestEventA();
        $eventB = new TestEventB();
        $eventC = new TestEventC();
        $eventD = new TestEventD();
        $eventE = new TestEventE();

        $this->eventDispatcher->dispatch($eventA);
        $event = $spyEventListener->shiftEvent();
        $this->assertSame($event, $eventA);
        $event = $spyEventListener->shiftEvent();
        $this->assertNull($event);

        $this->eventDispatcher->dispatch($eventB, $eventC);
        $event = $spyEventListener->shiftEvent();
        $this->assertSame($event, $eventB);
        $event = $spyEventListener->shiftEvent();
        $this->assertSame($event, $eventC);
        $event = $spyEventListener->shiftEvent();
        $this->assertNull($event);

        $this->eventDispatcher->dispatch($eventD, $eventE);
        $event = $spyEventListener->shiftEvent();
        $this->assertSame($event, $eventD);
        $event = $spyEventListener->shiftEvent();
        $this->assertSame($event, $eventE);
        $event = $spyEventListener->shiftEvent();
        $this->assertNull($event);
    }

    public function testItCallsListenersWhenDispatchesChildEvent(): void
    {
        $spyEventListener = new SpyEventListener($this->eventDispatcher);

        $childEvent = new TestChildEventE();

        $this->eventDispatcher->dispatch($childEvent);
        $event = $spyEventListener->shiftEvent();
        $this->assertSame($event, $childEvent);
        $event = $spyEventListener->shiftEvent();
        $this->assertNull($event);
    }

    private function expectExceptionForNonEventClass(string $actionName): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(\sprintf(
            'Invalid event class to %s event listener: expected sub-class of "%s", "%s" given.',
            $actionName,
            AbstractEvent::class,
            \stdClass::class,
        ));
    }
}
readonly class TestEventA extends AbstractEvent {}
readonly class TestEventB extends AbstractEvent {}
readonly class TestEventC extends AbstractEvent {}
readonly class TestEventD extends AbstractEvent {}
readonly class TestEventE extends AbstractEvent {}
readonly class TestChildEventE extends TestEventE {}

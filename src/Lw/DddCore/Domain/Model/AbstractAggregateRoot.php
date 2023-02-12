<?php

declare(strict_types=1);

namespace Lw\DddCore\Domain\Model;

/**
 * @author Nikolay Ryabkov <nikolay.ryabkov@sibers.com>
 */
abstract class AbstractAggregateRoot extends AbstractEntity
{
    private array $recordedEvents = [];

    /**
     * Returns recorded domain events and removes them from the aggregate root.
     */
    public function releaseRecordedEvents(): array
    {
        $recordedEvents       = $this->recordedEvents;
        $this->recordedEvents = [];

        return $recordedEvents;
    }

    /**
     * Records the domain event of the aggregate root for subsequent release.
     *
     * @throws \LogicException when the same event has already been recorded
     */
    protected function recordEvent(AbstractEvent $event): void
    {
        if (\in_array($event, $this->recordedEvents)) {
            throw new \LogicException(
                \sprintf('The same domain event of type "%s" already recorded.', \get_class($event))
            );
        }
        $this->recordedEvents[] = $event;
    }
}

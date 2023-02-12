<?php

declare(strict_types=1);

namespace Lw\DddCore\Domain\Model;

/**
 * @author Nikolay Ryabkov <nikolay.ryabkov@sibers.com>
 */
readonly abstract class AbstractEvent
{
    protected \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}

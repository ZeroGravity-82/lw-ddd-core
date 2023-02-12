<?php

declare(strict_types=1);

namespace Lw\DddCore\Domain\Model;

/**
 * @author Nikolay Ryabkov <nikolay.ryabkov@sibers.com>
 */
abstract class AbstractEntityId
{
    public function __construct(
        protected string $value,
    ) {
        $this->assertValidValue($value);
    }

    public function __toString(): string
    {
        return $this->value();
    }

    public function value(): string
    {
        return $this->value;
    }

    public function isEqual(self $id): bool
    {
        return $id->value() === $this->value();
    }

    protected function assertValidValue(string $value): void
    {
        $this->assertNotEmpty($value);
    }

    /**
     * @throws \InvalidArgumentException when the ID is an empty string
     */
    private function assertNotEmpty(string $value): void
    {
        if (\trim($value) === '') {
            throw new \InvalidArgumentException('Domain entity ID cannot be empty string.');
        }
    }
}

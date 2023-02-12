<?php

declare(strict_types=1);

namespace Lw\DddCore\Tests\Domain\Model;

use Lw\DddCore\Domain\Model\Exception as DomainException;
use Lw\DddCore\Domain\Model\NotFoundException;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <nikolay.ryabkov@sibers.com>
 */
class NotFoundExceptionTest extends TestCase
{
    public function testItExtendsDomainExceptionClass(): void
    {
        $notFoundException = new NotFoundException();
        $this->assertInstanceOf(DomainException::class, $notFoundException);
    }
}

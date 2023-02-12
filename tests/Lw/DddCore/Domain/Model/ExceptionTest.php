<?php

declare(strict_types=1);

namespace Lw\DddCore\Tests\Domain\Model;

use Lw\DddCore\Domain\Model\Exception as DomainException;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <nikolay.ryabkov@sibers.com>
 */
class ExceptionTest extends TestCase
{
    public function testItExtendsGenericExceptionClass(): void
    {
        $exception = new DomainException();
        $this->assertInstanceOf(\Exception::class, $exception);
    }
}

<?php

declare(strict_types=1);

namespace Lw\DddCore\Domain\Model;

/**
 * @author Nikolay Ryabkov <nikolay.ryabkov@sibers.com>
 */
interface IdentityGeneratorInterface
{
    public function getNextIdentity(): string;
}

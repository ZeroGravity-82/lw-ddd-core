<?php

declare(strict_types=1);

namespace Lw\DddCore\Domain\Model;

/**
 * @author Nikolay Ryabkov <nikolay.ryabkov@sibers.com>
 */
abstract class AbstractEntityFactory
{
    public function __construct(
        protected IdentityGeneratorInterface $identityGenerator,
    ) {}
}

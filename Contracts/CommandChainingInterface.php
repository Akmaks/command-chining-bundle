<?php

declare(strict_types=1);

namespace Akmaks\Bundle\CommandChainingBundle\Contracts;

interface CommandChainingInterface
{
    /**
     * Is master command in chain
     *
     * @return bool
     */
    public function isMasterCommand(): bool;
}

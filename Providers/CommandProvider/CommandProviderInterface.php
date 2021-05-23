<?php

declare(strict_types=1);

namespace Akmaks\Bundle\CommandChainingBundle\Providers\CommandProvider;

use Symfony\Component\Console\Command\Command;

interface CommandProviderInterface
{
    /**
     * Get next command in chain
     *
     * @param Command $command Next command in chain
     *
     * @return array<Command>
     */
    public function getChainCommands(Command $command): array;

    /**
     * Is object console command from chain
     *
     * @param mixed $command Console command
     *
     * @return bool
     */
    public function isCommandFromChain(mixed $command): bool;
}

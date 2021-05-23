<?php

declare(strict_types=1);

namespace Akmaks\Bundle\CommandChainingBundle\Providers\CommandProvider;

use Akmaks\Bundle\CommandChainingBundle\Contracts\CommandChainingInterface;
use Akmaks\Bundle\CommandChainingBundle\Exceptions\NotConfiguredMasterException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CommandProvider implements CommandProviderInterface
{
    /**
     * List of chains
     *
     * @var array
     */
    protected array $chains;

    /**
     * Client constructor.
     *
     * @param array $chains List of chains
     */
    public function __construct(array $chains)
    {
        $this->chains = $chains;
    }

    /**
     * Get next command in chain
     *
     * @param Command $command Next command in chain
     *
     * @return array<Command>
     * @throws NotConfiguredMasterException
     */
    public function getChainCommands(Command $command): array
    {
        if (isset($this->chains[get_class($command)]) === false
            || is_array($this->chains[get_class($command)]) === false
        ) {
            throw new NotConfiguredMasterException(get_class($command));
        }

        $chainCommands = [];
        $appCommands   = $this->getAppCommands($command);


        foreach ($this->chains[get_class($command)] as $chainCommandNamespace) {
            if (isset($appCommands[$chainCommandNamespace]) === true) {
                $chainCommands[] = $appCommands[$chainCommandNamespace];
            }
        }

        return $chainCommands;
    }

    /**
     * Is object console command from chain
     *
     * @param mixed $command Some command
     *
     * @return bool
     */
    public function isCommandFromChain(mixed $command): bool
    {
        if (($command instanceof CommandChainingInterface) === false
            || ($command instanceof Command) === false
        ) {
            return false;
        }

        return true;
    }

    /**
     * Returns app console commands
     *
     * @param Command $command Console command
     *
     * @return array
     */
    protected function getAppCommands(Command $command): array
    {
        $appCommands = [];

        foreach ($command->getApplication()->all() as $appCommand) {
            $appCommands[get_class($appCommand)] = $appCommand;
        }

        return $appCommands;
    }
}

<?php

declare(strict_types=1);

namespace Akmaks\Bundle\CommandChainingBundle\EventListener;

use Akmaks\Bundle\CommandChainingBundle\Contracts\CommandChainingInterface;
use Akmaks\Bundle\CommandChainingBundle\Providers\CommandProvider\CommandProviderInterface;
use Akmaks\Bundle\CommandChainingBundle\Exceptions\NotMasterCommandException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ConsoleListener implements EventSubscriberInterface
{
    /**
     * Command provider
     *
     * @var CommandProviderInterface
     */
    protected CommandProviderInterface $commandProvider;

    /**
     * Logger
     *
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * ConsoleSubscriber constructor.
     *
     * @param \Psr\Log\LoggerInterface $logger Chain logger
     * @param CommandProviderInterface $commandProvider Command provider
     */
    public function __construct(LoggerInterface $logger, CommandProviderInterface $commandProvider)
    {
        $this->logger          = $logger;
        $this->commandProvider = $commandProvider;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * ['eventName' => 'methodName']
     *  * ['eventName' => ['methodName', $priority]]
     *  * ['eventName' => [['methodName1', $priority], ['methodName2']]]
     *
     * The code must not depend on runtime state as it will only be called at compile time.
     * All logic depending on runtime state must be put into the individual methods handling the events.
     *
     * @return array The event names to listen to
     * @inheritdoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ConsoleEvents::COMMAND   => 'onConsoleCommand',
            ConsoleEvents::TERMINATE => 'onConsoleTerminate',
        ];
    }

    /**
     * Before executing console command event handler
     *
     * @param ConsoleEvent $event Console event
     *
     * @return void
     * @throws NotMasterCommandException
     */
    public function onConsoleCommand(ConsoleEvent $event): void
    {
        $command = $event->getCommand();

        if ($this->commandProvider->isCommandFromChain($command) === false) {
            return;
        }

        if ($command instanceof CommandChainingInterface
            && $command->isMasterCommand() === false
        ) {
            $event->stopPropagation();

            throw new NotMasterCommandException(get_class($command));
        }

        if ($command instanceof Command) {
            $this->logRegisteredChainCommands($command);
        }
    }

    /**
     * Before terminating console command event handler
     *
     * @param ConsoleEvent $event Console event
     *
     * @return void
     * @throws \Exception
     */
    public function onConsoleTerminate(ConsoleEvent $event): void
    {
        $command = $event->getCommand();

        if ($this->commandProvider->isCommandFromChain($command) === false) {
            return;
        }

        $chainCommands = $this->commandProvider->getChainCommands($command);
        $this->logger->notice(
            sprintf(
                'Executing %s chain members',
                $command->getName()
            )
        );

        foreach ($chainCommands as $chainCommand) {
            if ($this->commandProvider->isCommandFromChain($chainCommand) === true) {
                $chainCommand->run($event->getInput(), $event->getOutput());
            }
        }

        if ($command instanceof Command) {
            $this->logger->notice(
                sprintf(
                    'Execution of %s chain completed',
                    $command->getName()
                )
            );
        }
    }

    /**
     * Log registered chain commands
     *
     * @param Command $command Console command
     *
     * @return void
     */
    protected function logRegisteredChainCommands(Command $command): void
    {
        $this->logger->notice(
            sprintf(
                '%s is a master command of a command chain that has registered member commands',
                $command->getName()
            )
        );

        $chainCommands        = $this->commandProvider->getChainCommands($command);
        $chainCommandsAliases = array_map(
            function (Command $command) {
                return $command->getName();
            },
            $chainCommands
        );

        $this->logger->notice(
            sprintf(
                '%s: are registered as a members of %s command chain',
                implode(', ', $chainCommandsAliases),
                $command->getName()
            )
        );

        $this->logger->notice(
            sprintf(
                'Executing %s command itself first',
                $command->getName()
            )
        );
    }
}

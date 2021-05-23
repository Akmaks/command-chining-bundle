<?php

declare(strict_types=1);

namespace Akmaks\Bundle\CommandChainingBundle\Tests\Unit\EventListener;

use Akmaks\Bundle\CommandChainingBundle\Contracts\CommandChainingInterface;
use Akmaks\Bundle\CommandChainingBundle\EventListener\ConsoleListener;
use Akmaks\Bundle\CommandChainingBundle\Exceptions\NotMasterCommandException;
use Akmaks\Bundle\CommandChainingBundle\Tests\Unit\AbstractUnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;

class ConsoleListenerTest extends AbstractUnitTestCase
{
    /**
     * Console listener for testing
     *
     * @var ConsoleListener
     */
    protected ConsoleListener $consoleListener;

    /**
     * CommandProvider mock
     *
     * @var MockObject|CommandChainingInterface
     */
    protected MockObject $commandProvider;

    /**
     * Logger mock
     *
     * @var MockObject|LoggerInterface
     */
    protected MockObject $logger;

    /**
     * Set up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->logger          = $this->getLoggerMock();
        $this->commandProvider = $this->getCommandProviderMock();

        $this->consoleListener = new ConsoleListener(
            $this->logger,
            $this->commandProvider
        );
    }

    /**
     * Test normal behavior of getSubscribedEvents method
     *
     * @return void
     */
    public function testGetSubscribedEvents(): void
    {
        $this->assertEquals(
            $this->getSubscribedEventsData(),
            $this->consoleListener::getSubscribedEvents()
        );
    }

    /**
     * Test normal behavior of onConsoleCommand method
     *
     * @return void
     * @throws \Akmaks\Bundle\CommandChainingBundle\Exceptions\NotMasterCommandException
     */
    public function testOnConsoleCommandNormal(): void
    {
        $command = $this->getCommandChainingInterfaceMock();

        $consoleEvent = $this->getConsoleEventMock();
        $consoleEvent->expects($this->once())
                     ->method('getCommand')
                     ->willReturn($command);

        $this->commandProvider->expects(($this->once()))
                              ->method('isCommandFromChain')
                              ->with($command)
                              ->willReturn(true);

        $command->expects(($this->once()))
                ->method('isMasterCommand')
                ->willReturn(true);

        $this->consoleListener->onConsoleCommand($consoleEvent);
    }

    /**
     * Test empty command in event of onConsoleCommand method
     *
     * @return void
     * @throws \Akmaks\Bundle\CommandChainingBundle\Exceptions\NotMasterCommandException
     */
    public function testOnConsoleCommandWithEmptyCommandInEvent()
    {
        $command = $this->getCommandChainingInterfaceMock();

        $consoleEvent = $this->getConsoleEventMock();
        $consoleEvent->expects($this->once())
                     ->method('getCommand')
                     ->willReturn(null);

        $this->commandProvider->expects(($this->once()))
                              ->method('isCommandFromChain')
                              ->willReturn(false);

        $command->expects(($this->never()))
                ->method('isMasterCommand');

        $this->consoleListener->onConsoleCommand($consoleEvent);
    }

    /**
     * Test throw NotMasterCommandException of onConsoleCommand method
     *
     * @return void
     * @throws \Akmaks\Bundle\CommandChainingBundle\Exceptions\NotMasterCommandException
     */
    public function testOnConsoleCommandWithNotMasterCommandInEvent()
    {
        $command = $this->getCommandChainingInterfaceMock();

        $consoleEvent = $this->getConsoleEventMock();
        $consoleEvent->expects($this->once())
                     ->method('getCommand')
                     ->willReturn($command);

        $this->commandProvider->expects(($this->once()))
                              ->method('isCommandFromChain')
                              ->with($command)
                              ->willReturn(true);

        $command->expects($this->once())
                ->method('isMasterCommand')
                ->willReturn(false);

        $consoleEvent->expects($this->once())
                     ->method('stopPropagation');

        $this->expectException(NotMasterCommandException::class);

        $this->consoleListener->onConsoleCommand($consoleEvent);
    }

    /**
     * Test normal behavior of onConsoleTerminate method
     *
     * @return void
     * @throws \Exception
     */
    public function testOnConsoleTerminateNormal()
    {
        $command      = $this->getCommandMock();
        $chainCommand = $this->getCommandMock();

        $input  = $this->getInputMock();
        $output = $this->getOutputMock();

        $consoleEvent = $this->getConsoleEventMock();
        $consoleEvent->expects($this->once())
                     ->method('getCommand')
                     ->willReturn($command);
        $consoleEvent->expects($this->once())
                     ->method('getInput')
                     ->willReturn($input);
        $consoleEvent->expects($this->once())
                     ->method('getOutput')
                     ->willReturn($output);

        $this->commandProvider->expects($this->exactly(2))
                              ->method('isCommandFromChain')
                              ->with($command)
                              ->willReturn(true);

        $this->commandProvider->expects($this->once())
                              ->method('getChainCommands')
                              ->with($command)
                              ->willReturn([$chainCommand]);

        $chainCommand->expects($this->once())->method('run');

        $this->consoleListener->onConsoleTerminate($consoleEvent);
    }

    /**
     * Test command not in chain of onConsoleTerminate method
     *
     * @return void
     * @throws \Exception
     */
    public function testOnConsoleTerminateNoCommandChain()
    {
        $command = $this->getCommandMock();

        $consoleEvent = $this->getConsoleEventMock();
        $consoleEvent->expects($this->once())
                     ->method('getCommand')
                     ->willReturn($command);

        $this->commandProvider->expects($this->once())
                              ->method('isCommandFromChain')
                              ->with($command)
                              ->willReturn(false);

        $this->commandProvider->expects($this->never())->method('getChainCommands');

        $this->consoleListener->onConsoleTerminate($consoleEvent);
    }

    /**
     * Test empty chain of onConsoleTerminate method
     *
     * @return void
     * @throws \Exception
     */
    public function testOnConsoleTerminateWithoutChainCommands()
    {
        $command      = $this->getCommandMock();
        $chainCommand = $this->getCommandMock();

        $consoleEvent = $this->getConsoleEventMock();
        $consoleEvent->expects($this->once())
                     ->method('getCommand')
                     ->willReturn($command);

        $this->commandProvider->expects($this->exactly(1))
                              ->method('isCommandFromChain')
                              ->with($command)
                              ->willReturn(true);

        $this->commandProvider->expects($this->once())
                              ->method('getChainCommands')
                              ->with($command)
                              ->willReturn([]);

        $chainCommand->expects($this->never())->method('run');

        $this->consoleListener->onConsoleTerminate($consoleEvent);
    }

    /**
     * Returns subscribedEvents data
     *
     * @return string[]
     */
    protected function getSubscribedEventsData(): array
    {
        return [
            'console.command'   => 'onConsoleCommand',
            'console.terminate' => 'onConsoleTerminate',
        ];
    }
}

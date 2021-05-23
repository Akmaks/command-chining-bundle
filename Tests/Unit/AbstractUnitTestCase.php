<?php

declare(strict_types=1);

namespace Akmaks\Bundle\CommandChainingBundle\Tests\Unit;

use Akmaks\Bundle\CommandChainingBundle\Contracts\CommandChainingInterface;
use Akmaks\Bundle\CommandChainingBundle\Providers\CommandProvider\CommandProviderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleEvent;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AbstractUnitTestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * Returns logger mock
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|LoggerInterface
     */
    protected function getLoggerMock(): MockObject
    {
        return $this->getMockBuilder(LoggerInterface::class)
                    ->disableOriginalConstructor()
                    ->getMock();
    }

    /**
     * Returns command provider mock
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|CommandProviderInterface
     */
    protected function getCommandProviderMock(): MockObject
    {
        return $this->getMockBuilder(CommandProviderInterface::class)
                    ->disableOriginalConstructor()
                    ->getMock();
    }

    /**
     * Returns console event mock
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|ConsoleEvent
     */
    protected function getConsoleEventMock(): MockObject
    {
        return $this->getMockBuilder(ConsoleEvent::class)
                    ->disableOriginalConstructor()
                    ->getMock();
    }

    /**
     * Returns console command mock
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|Command
     */
    protected function getCommandMock(string $fakeName = ''): MockObject
    {
        return $this->getMockBuilder(Command::class)
                    ->disableOriginalConstructor()
                    ->setMockClassName($fakeName)
                    ->getMock();
    }

    /**
     * Returns CommandChainingInterface mock
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|CommandChainingInterface
     */
    protected function getCommandChainingInterfaceMock(): MockObject
    {
        return $this->getMockBuilder(CommandChainingInterface::class)
                    ->disableOriginalConstructor()
                    ->getMock();
    }

    /**
     * Returns InputInterface mock
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|InputInterface
     */
    protected function getInputMock(): MockObject
    {
        return $this->getMockBuilder(InputInterface::class)
                    ->disableOriginalConstructor()
                    ->getMock();
    }

    /**
     * Returns OutputInterface mock
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|OutputInterface
     */
    protected function getOutputMock(): MockObject
    {
        return $this->getMockBuilder(OutputInterface::class)
                    ->disableOriginalConstructor()
                    ->getMock();
    }
}

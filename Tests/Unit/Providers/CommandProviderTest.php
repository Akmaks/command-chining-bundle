<?php

declare(strict_types=1);

namespace Akmaks\Bundle\CommandChainingBundle\Tests\Unit\Providers;

use Akmaks\Bundle\CommandChainingBundle\Contracts\CommandChainingInterface;
use Akmaks\Bundle\CommandChainingBundle\Exceptions\NotConfiguredMasterException;
use Akmaks\Bundle\CommandChainingBundle\Providers\CommandProvider\CommandProvider;
use Akmaks\Bundle\CommandChainingBundle\Tests\Unit\AbstractUnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Console\Command\Command;

class CommandProviderTest extends AbstractUnitTestCase
{
    /**
     * Test normal behavior of getChainCommandsNormal method
     *
     * @throws \Akmaks\Bundle\CommandChainingBundle\Exceptions\NotConfiguredMasterException
     */
    public function testGetChainCommandsNormal()
    {
        [
            $masterCommand,
            $chainCommand,
            $notExistMasterCommand,
            $chains,
            $appCommandsMap,
        ] = $this->getTestData();

        $commandProvider = $this->getCommandProviderMockForTesting($chains);
        $commandProvider->expects($this->once())
                        ->method('getAppCommands')
                        ->with($masterCommand)
                        ->willReturn([get_class($chainCommand) => $chainCommand]);

        $actual = $commandProvider->getChainCommands($masterCommand);

        $this->assertEquals([$chainCommand], $actual);
    }

    /**
     * Test throwing NotMasterCommandException of getChainCommandsNormal method
     *
     * @throws \Akmaks\Bundle\CommandChainingBundle\Exceptions\NotConfiguredMasterException
     */
    public function testGetChainCommandsWithNotExistMasterCommandInChain()
    {
        [
            $masterCommand,
            $chainCommand,
            $notExistMasterCommand,
            $chains,
            $appCommandsMap,
        ] = $this->getTestData();

        $commandProvider = $this->getCommandProviderMockForTesting($chains);

        $this->expectException(NotConfiguredMasterException::class);

        $commandProvider->getChainCommands($notExistMasterCommand);
    }

    /**
     * Test normal behavior of isCommandFromChain method
     *
     * @dataProvider objectProvider
     */
    public function testIsCommandFromChainNormal($valueForTesting, bool $expected)
    {
        [
            $masterCommand,
            $chainCommand,
            $notExistMasterCommand,
            $chains,
            $appCommandsMap,
        ] = $this->getTestData();

        $commandProvider = new CommandProvider($chains);

        $this->assertEquals($expected, $commandProvider->isCommandFromChain($valueForTesting));
    }

    /**
     * Returns command provider mock for testing
     *
     * @param array $chains List of chains
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|CommandProvider
     */
    protected function getCommandProviderMockForTesting(array $chains): MockObject
    {
        return $this->getMockBuilder(CommandProvider::class)
                    ->setConstructorArgs([$chains])
                    ->onlyMethods(['getAppCommands'])
                    ->getMock();
    }

    /**
     * Test objects provider
     *
     * @return array[]
     */
    public function objectProvider(): array
    {
        return [
            [
                new class() extends Command implements CommandChainingInterface {
                    public function isMasterCommand(): bool
                    {
                        return true;
                    }
                },
                true,
            ],
            [
                new class() extends Command {
                },
                false,
            ],
            [
                new class() implements CommandChainingInterface {
                    public function isMasterCommand(): bool
                    {
                        return true;
                    }
                },
                false,
            ],
            [
                new class() {
                    public function isMasterCommand(): bool
                    {
                        return true;
                    }
                },
                false,
            ],
            [
                'sdgsergse',
                false,
            ],
            [
                null,
                false,
            ],
            [
                1,
                false,
            ],
        ];
    }

    /**
     * Returns test data
     *
     * @return array
     */
    protected function getTestData(): array
    {
        $masterCommand         = $this->getCommandMock('MasterCommand');
        $chainCommand          = $this->getCommandMock('ChainCommand');
        $notExistMasterCommand = $this->getCommandMock('NotExistMasterCommand');
        $chains                = [
            get_class($masterCommand) => [
                get_class($chainCommand),
            ],
        ];
        $appCommandsMap        = [get_class($chainCommand) => $chainCommand];

        return [
            $masterCommand,
            $chainCommand,
            $notExistMasterCommand,
            $chains,
            $appCommandsMap,
        ];
    }
}

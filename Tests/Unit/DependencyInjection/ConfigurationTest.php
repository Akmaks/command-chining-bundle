<?php

declare(strict_types=1);

namespace Akmaks\Bundle\CommandChainingBundle\Tests\Unit\DependencyInjection;

use Akmaks\Bundle\CommandChainingBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Akmaks\Bundle\CommandChainingBundle\Tests\Unit\AbstractUnitTestCase;

class ConfigurationTest extends AbstractUnitTestCase
{
    /**
     * Test getConfigTreeBuilder method
     *
     * @return void
     */
    public function testGetConfigTreeBuilder(): void
    {
        $configuration = new Configuration();

        $this->assertInstanceOf(
            TreeBuilder::class,
            $configuration->getConfigTreeBuilder()
        );
    }
}

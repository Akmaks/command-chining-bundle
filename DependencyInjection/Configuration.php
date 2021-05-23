<?php

declare(strict_types=1);

namespace Akmaks\Bundle\CommandChainingBundle\DependencyInjection;

use Akmaks\Bundle\CommandChainingBundle\AkmaksCommandChainingBundle;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return TreeBuilder The tree builder
     * @inheritdoc
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder(AkmaksCommandChainingBundle::ALIAS);
        $treeBuilder->getRootNode()
                        ->children()
                            ->arrayNode('parameters')
                                ->children()
                                    ->arrayNode('chains')->end()
                            ->end()
                    ->end();

        return $treeBuilder;
    }
}

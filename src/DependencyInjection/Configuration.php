<?php

namespace Vinorcola\HelperBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('vinorcola_helper');

        $rootNode
            ->children()
                ->scalarNode('separator')
                    ->defaultValue('.')
                ->end() // separator
                ->scalarNode('attribute_prefix')
                    ->defaultValue('attribute')
                    ->cannotBeEmpty()
                ->end() // attribute_prefix
            ->end()
        ;

        return $treeBuilder;
    }

}

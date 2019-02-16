<?php

namespace Onixcat\Bundle\RestApiBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package Onixcat\Bundle\RestApiBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('onixcat_rest_api');

        $rootNode
            ->children()
            ->arrayNode('prefixes')->isRequired()->prototype('scalar')->end()
            ->end();

        return $treeBuilder;
    }
}

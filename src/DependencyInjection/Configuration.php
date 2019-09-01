<?php
/**
 * Created by PhpStorm.
 * Filename: Configuration.php
 * User: Andrei Gache
 * Email: andrei.gache.99@gmail.com
 * Website: https://www.andrei-gache.com/
 * Date: 01/09/19
 * Time: 19:41
 */

namespace App\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('console_security');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('create_admin')
                    ->children()
                        ->scalarNode('password')->end()
                    ->end()
                ->end()
            ->end()
        ;
        return $treeBuilder;
    }
}
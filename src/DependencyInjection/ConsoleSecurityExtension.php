<?php
/**
 * Created by PhpStorm.
 * Filename: ConsoleSecurityExtenstion.php
 * User: Andrei Gache
 * Email: andrei.gache.99@gmail.com
 * Website: https://www.andrei-gache.com/
 * Date: 01/09/19
 * Time: 19:01
 */

namespace App\DependencyInjection;


use App\Model\ConsoleSecurityConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;

class ConsoleSecurityExtension extends Extension
{

    /**
     * Loads a specific configuration.
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $container->setDefinition('c.console_security', new Definition(
            ConsoleSecurityConfig::class,
            [
                $config['create_admin']['password'],
            ]
        ));


    }

    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return parent::getConfiguration($config, $container);
    }
}
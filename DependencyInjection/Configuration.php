<?php

namespace Akeneo\Bundle\BatchBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/MIT MIT
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $root = $treeBuilder->root('akeneo_batch');

        $root
            ->children()
                ->booleanNode('enable_mail_notification')->defaultFalse()->end()
                ->scalarNode('sender_email')->defaultValue('admin@example.com')->end()
                ->arrayNode('security')
                    ->children()
                        ->scalarNode('user_provider')->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}

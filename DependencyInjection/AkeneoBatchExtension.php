<?php

namespace Akeneo\Bundle\BatchBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Batch bundle services configuration declaration
 *
 * @author    Benoit Jacquemont <benoit@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/MIT MIT
 */
class AkeneoBatchExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $this->configureNotifier($container, $config);
        $this->configureAuthenticationListener($container, $config);
    }

    /**
     * Register the mail notifier when enabled through configuration
     *
     * @param ContainerBuilder $container
     * @param array            $config
     */
    private function configureNotifier(ContainerBuilder $container, array $config)
    {
        $container->setParameter('akeneo_batch.mail_notifier.sender_email', $config['sender_email']);
        if ($config['enable_mail_notification']) {
            $container
                ->getDefinition('akeneo_batch.mail_notifier')
                ->addTag('akeneo_batch.notifier');
        }
    }

    /**
     * Register batch command authentication listener
     * and set its user provider
     *
     * @param ContainerBuilder $container
     * @param array            $config
     */
    private function configureAuthenticationListener(ContainerBuilder $container, array $config)
    {
        if (isset($config['security']['user_provider'])) {
            $definition = $container->getDefinition('akeneo_batch.command.authenticate_user_listener');

            $args = $definition->getArguments();
            $args[] = new Reference($config['security']['user_provider']);
            $definition->setArguments($args);

            $definition
                ->addTag(
                    'kernel.event_listener',
                    array(
                        'event' => 'console.command',
                        'method' => 'authenticate',
                        'priority' => 127,
                    )
                );
        }
    }
}

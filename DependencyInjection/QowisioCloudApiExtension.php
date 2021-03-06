<?php

namespace A5sys\QowisioCloudApiBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class QowisioCloudApiExtension extends Extension
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

        $container->setParameter('qowisio.cloud.api.auth.endpoint', $config['endpoints']['authentication']);
        $container->setParameter('qowisio.cloud.api.data.endpoint', $config['endpoints']['data']);

        $container->setParameter('qowisio.cloud.api.auth.email', $config['authentication']['email']);
        $container->setParameter('qowisio.cloud.api.auth.password', $config['authentication']['password']);
    }
}

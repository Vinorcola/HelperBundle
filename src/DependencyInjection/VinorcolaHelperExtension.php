<?php

namespace Vinorcola\HelperBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Vinorcola\HelperBundle\Model\RouteNamespaceModel;
use Vinorcola\HelperBundle\Model\TranslationModel;

class VinorcolaHelperExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $processedConfig = $this->processConfiguration(new Configuration(), $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('services.yaml');

        $container
            ->getDefinition(RouteNamespaceModel::class)
            ->setArgument('$separator', $processedConfig['separator']);
        $container
            ->getDefinition(TranslationModel::class)
            ->setArgument('$attributePrefix', $processedConfig['attribute_prefix'])
            ->setArgument('$separator', $processedConfig['separator']);
    }
}

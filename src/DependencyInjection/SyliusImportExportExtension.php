<?php

declare(strict_types=1);

namespace FriendsOfSylius\SyliusImportExportPlugin\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class SyliusImportExportExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $this->processConfiguration($configuration, $configs);

        if (!class_exists('Port\Csv\CsvReaderFactory')) {
            $container->removeDefinition('sylius.factory.csv_reader');
            $container->removeDefinition('sylius.importer.payment_methods.csv');
            $container->removeDefinition('sylius.importer.tax_categories.csv');
        }

        if (!class_exists('Port\Csv\ExcelReaderFactory')) {
            $container->removeDefinition('sylius.factory.excel_reader');
            $container->removeDefinition('sylius.importer.payment_methods.excel');
            $container->removeDefinition('sylius.importer.tax_categories.excel');
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }
}

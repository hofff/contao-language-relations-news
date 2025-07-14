<?php

declare(strict_types=1);

namespace Hofff\Contao\LanguageRelations\News\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/** @SuppressWarnings(PHPMD.LongClassName) */
final class HofffContaoLanguageRelationsNewsExtension extends Extension
{
    /** {@inheritDoc} */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('services.xml');
    }
}

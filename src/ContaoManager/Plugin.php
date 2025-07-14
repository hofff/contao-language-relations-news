<?php

declare(strict_types=1);

namespace Hofff\Contao\LanguageRelations\News\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\NewsBundle\ContaoNewsBundle;
use Hofff\Contao\LanguageRelations\HofffContaoLanguageRelationsBundle;
use Hofff\Contao\LanguageRelations\News\HofffContaoLanguageRelationsNewsBundle;

final class Plugin implements BundlePluginInterface
{
    /** {@inheritDoc} */
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(HofffContaoLanguageRelationsNewsBundle::class)
                ->setLoadAfter([
                    ContaoCoreBundle::class,
                    HofffContaoLanguageRelationsBundle::class,
                    ContaoNewsBundle::class,
                ]),
        ];
    }
}

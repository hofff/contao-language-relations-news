<?php

declare(strict_types=1);

namespace Hofff\Contao\LanguageRelations\News\DCA;

class NewsDCA
{
    /** @SuppressWarnings(PHPMD.Superglobals) */
    public function hookLoadDataContainer(string $table): void
    {
        if ($table !== 'tl_news') {
            return;
        }

        $palettes = &$GLOBALS['TL_DCA']['tl_news']['palettes'];
        foreach ($palettes as $key => &$palette) {
            if ($key === '__selector__') {
                continue;
            }

            $palette .= ';{hofff_language_relations_legend}';
            $palette .= ',hofff_language_relations';
        }

        unset($palette, $palettes);
    }
}

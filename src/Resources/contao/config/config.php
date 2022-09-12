<?php

declare(strict_types=1);

use Hofff\Contao\LanguageRelations\News\Database\Installer;
use Hofff\Contao\LanguageRelations\News\DCA\NewsDCA;
use Hofff\Contao\LanguageRelations\News\LanguageRelationsNews;

$GLOBALS['BE_MOD']['content']['news']['stylesheet'][] = 'bundles/hofffcontaolanguagerelations/css/style.css';

$GLOBALS['TL_HOOKS']['loadDataContainer']['hofff_language_relations_news']                          =
    [NewsDCA::class, 'hookLoadDataContainer'];
$GLOBALS['TL_HOOKS']['sqlCompileCommands']['hofff_language_relations_news']                         =
    [Installer::class, 'hookSQLCompileCommands'];
$GLOBALS['TL_HOOKS']['hofff_language_relations_language_switcher']['hofff_language_relations_news'] =
    [LanguageRelationsNews::class, 'hookLanguageSwitcher'];

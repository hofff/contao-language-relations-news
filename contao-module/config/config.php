<?php

$GLOBALS['BE_MOD']['content']['news']['stylesheet'][]
	= 'system/modules/hofff_language_relations/assets/css/style.css';

$GLOBALS['TL_HOOKS']['loadDataContainer']['hofff_language_relations_news']
	= [ 'Hofff\\Contao\\LanguageRelations\\News\\DCA\\NewsDCA', 'hookLoadDataContainer' ];
$GLOBALS['TL_HOOKS']['sqlCompileCommands']['hofff_language_relations_news']
	= [ 'Hofff\\Contao\\LanguageRelations\\News\\Database\\Installer', 'hookSQLCompileCommands' ];

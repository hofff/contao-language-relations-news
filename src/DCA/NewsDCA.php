<?php

namespace Hofff\Contao\LanguageRelations\News\DCA;

/**
 * @author Oliver Hoff <oliver@hofff.com>
 */
class NewsDCA {

	/**
	 * @param string $table
	 * @return void
	 */
	public function hookLoadDataContainer($table) {
		if($table != 'tl_news') {
			return;
		}

		$palettes = &$GLOBALS['TL_DCA']['tl_news']['palettes'];
		foreach($palettes as $key => &$palette) {
			if($key != '__selector__') {
				$palette .= ';{hofff_language_relations_legend}';
				$palette .= ',hofff_language_relations';
			}
		}
		unset($palette, $palettes);
	}

}

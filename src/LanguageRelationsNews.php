<?php

namespace Hofff\Contao\LanguageRelations\News;

use Hofff\Contao\LanguageRelations\Relations;

/**
 * @author Oliver Hoff <oliver@hofff.com>
 * @deprecated
 */
class LanguageRelationsNews {

	/**
	 * @var Relations
	 */
	private static $relations;

	/**
	 * @return Relations
	 */
	public static function getRelationsInstance() {
		isset(self::$relations) || self::$relations = new Relations(
			'tl_hofff_language_relations_news',
			'hofff_language_relations_news_item',
			'hofff_language_relations_news_relation'
		);
		return self::$relations;
	}

}

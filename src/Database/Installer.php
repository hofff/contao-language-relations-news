<?php

namespace Hofff\Contao\LanguageRelations\News\Database;

use Contao\Database;
use Hofff\Contao\LanguageRelations\Util\StringUtil;

/**
 * @author Oliver Hoff <oliver@hofff.com>
 */
class Installer {

	/**
	 * @param array $queries
	 * @return void
	 */
	public function hookSQLCompileCommands($queries) {
		$tables = array_flip(Database::getInstance()->listTables(null, true));

		if(!isset($tables['hofff_language_relations_news_item'])) {
			$queries['ALTER_CHANGE'][] = StringUtil::tabsToSpaces($this->getItemView());
		}
		if(!isset($tables['hofff_language_relations_news_relation'])) {
			$queries['ALTER_CHANGE'][] = StringUtil::tabsToSpaces($this->getRelationView());
		}
		if(!isset($tables['hofff_language_relations_news_aggregate'])) {
			$queries['ALTER_CHANGE'][] = StringUtil::tabsToSpaces($this->getAggregateView());
		}
		if(!isset($tables['hofff_language_relations_news_tree'])) {
			$queries['ALTER_CHANGE'][] = StringUtil::tabsToSpaces($this->getTreeView());
		}

		return $queries;
	}

	/**
	 * @return string
	 */
	protected function getItemView() {
		return <<<SQL
CREATE OR REPLACE VIEW hofff_language_relations_news_item AS

SELECT
	root_page.hofff_language_relations_group_id	AS group_id,
	root_page.id								AS root_page_id,
	page.id										AS page_id,
	news.id										AS item_id
FROM
	tl_news
	AS news
JOIN
	tl_news_archive
	AS news_archive
	ON news_archive.id = news.pid
JOIN
	tl_page
	AS page
	ON page.id = news_archive.jumpTo
JOIN
	tl_page
	AS root_page
	ON root_page.id = page.hofff_root_page_id
SQL;
	}

	/**
	 * @return string
	 */
	protected function getRelationView() {
		return <<<SQL
CREATE OR REPLACE VIEW hofff_language_relations_news_relation AS

SELECT
	item.group_id									AS group_id,
	item.root_page_id								AS root_page_id,
	item.page_id									AS page_id,
	item.item_id									AS item_id,
	related_item.item_id							AS related_item_id,
	related_item.page_id							AS related_page_id,
	related_item.root_page_id						AS related_root_page_id,
	related_item.group_id							AS related_group_id,
	item.root_page_id != related_item.root_page_id
		AND item.group_id = related_item.group_id	AS is_valid,
	reflected_relation.item_id IS NOT NULL			AS is_primary

FROM
	tl_hofff_language_relations_news
	AS relation
JOIN
	hofff_language_relations_news_item
	AS item
	ON item.item_id = relation.item_id
JOIN
	hofff_language_relations_news_item
	AS related_item
	ON related_item.item_id = relation.related_item_id

LEFT JOIN
	tl_hofff_language_relations_news
	AS reflected_relation
	ON reflected_relation.item_id = relation.related_item_id
	AND reflected_relation.related_item_id = relation.item_id
SQL;
	}

	/**
	 * @return string
	 */
	protected function getAggregateView() {
		return <<<SQL
CREATE OR REPLACE VIEW hofff_language_relations_news_aggregate AS

SELECT
	archive.id					AS aggregate_id,
	CONCAT('a', archive.id)		AS tree_root_id,
	root_page.id				AS root_page_id,
	grp.id						AS group_id,
	grp.title					AS group_title,
	root_page.language			AS language
FROM
	tl_news_archive
	AS archive
JOIN
	tl_page
	AS page
	ON page.id = archive.jumpTo
JOIN
	tl_page
	AS root_page
	ON root_page.id = page.hofff_root_page_id
JOIN
	tl_hofff_language_relations_group
	AS grp
	ON grp.id = root_page.hofff_language_relations_group_id
SQL;
	}

	/**
	 * @return string
	 */
	protected function getTreeView() {
		return <<<SQL
CREATE OR REPLACE VIEW hofff_language_relations_news_tree AS

SELECT
	0																AS pid,
	CONCAT('a', archive.id)											AS id,
	archive.title													AS title,
	0																AS selectable,
	root_page.hofff_language_relations_group_id						AS group_id,
	root_page.language												AS language,
	'archive'														AS type,
	NULL															AS date
FROM
	tl_news_archive
	AS archive
JOIN
	tl_page
	AS page
	ON page.id = archive.jumpTo
JOIN
	tl_page
	AS root_page
	ON root_page.id = page.hofff_root_page_id

UNION SELECT
	CONCAT('a', archive.id)											AS pid,
	CONCAT('a', archive.id, '_', YEAR(FROM_UNIXTIME(news.date)))	AS id,
	YEAR(FROM_UNIXTIME(news.date))									AS title,
	0																AS selectable,
	root_page.hofff_language_relations_group_id						AS group_id,
	root_page.language												AS language,
	'year'															AS type,
	YEAR(FROM_UNIXTIME(news.date))									AS date
FROM
	tl_news
	AS news
JOIN
	tl_news_archive
	AS archive
	ON archive.id = news.pid
JOIN
	tl_page
	AS page
	ON page.id = archive.jumpTo
JOIN
	tl_page
	AS root_page
	ON root_page.id = page.hofff_root_page_id
GROUP BY
	YEAR(FROM_UNIXTIME(news.date)),
	archive.id,
	root_page.language,
	root_page.hofff_language_relations_group_id

UNION SELECT
	CONCAT('a', archive.id, '_', YEAR(FROM_UNIXTIME(news.date)))	AS pid,
	news.id															AS id,
	news.headline													AS title,
	1																AS selectable,
	root_page.hofff_language_relations_group_id						AS group_id,
	root_page.language												AS language,
	'entry'															AS type,
	news.date														AS date
FROM
	tl_news
	AS news
JOIN
	tl_news_archive
	AS archive
	ON archive.id = news.pid
JOIN
	tl_page
	AS page
	ON page.id = archive.jumpTo
JOIN
	tl_page
	AS root_page
	ON root_page.id = page.hofff_root_page_id
SQL;
	}

}

<?php

namespace Hofff\Contao\LanguageRelations\News\Util;

use Contao\Config;
use Contao\Input;
use Contao\ModuleNews;
use Contao\NewsArchiveModel;
use Contao\NewsModel;
use Contao\PageModel;
use Hofff\Contao\LanguageRelations\Util\QueryUtil;

/**
 * @author Oliver Hoff <oliver@hofff.com>
 */
class ContaoNewsUtil extends ModuleNews {

	/**
	 */
	public function __construct() {
	}

	/**
	 * @param integer|null $jumpTo
	 * @return integer|null
	 */
	public static function findCurrentNews($jumpTo = null) {
		if(isset($_GET['items'])) {
			$idOrAlias = Input::get('items', false, true);
		} elseif(isset($_GET['auto_item']) && Config::get('useAutoItem')) {
			$idOrAlias = Input::get('auto_item', false, true);
		} else {
			return null;
		}

		$sql = <<<SQL
SELECT
	news.id			AS news_id,
	archive.jumpTo	AS archive_jump_to
FROM
	tl_news
	AS news
JOIN
	tl_news_archive
	AS archive
	ON archive.id = news.pid
WHERE
	news.id = ? OR news.alias = ?
SQL;
		$result = QueryUtil::query(
			$sql,
			null,
			[ $idOrAlias, $idOrAlias ]
		);

		if(!$result->numRows) {
			return null;
		}

		if($jumpTo === null || $jumpTo == $result->archive_jump_to) {
			return $result->news_id;
		}

		return null;
	}

	/**
	 * @param NewsModel $news
	 * @return string
	 */
	public static function getNewsURL(NewsModel $news) {
		static $instance;
		$instance || $instance = new self;
		return $instance->generateNewsUrl($news);
	}

	/**
	 * @param array $relatedNews
	 * @return void
	 */
	public static function prefetchNewsModels(array $ids) {
		$archives = [];
		foreach(NewsModel::findMultipleByIds(array_values($ids)) as $news) {
			$archives[] = $news->pid;
		}

		$pages = [];
		foreach(NewsArchiveModel::findMultipleByIds($archives) as $archive) {
			$archive->jumpTo && $pages[] = $archive->jumpTo;
		}

		PageModel::findMultipleByIds($pages);
	}

	/**
	 * @see \Contao\Module::compile()
	 */
	protected function compile() {
	}

}

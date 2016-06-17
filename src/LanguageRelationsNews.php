<?php

namespace Hofff\Contao\LanguageRelations\News;

use Hofff\Contao\LanguageRelations\Module\ModuleLanguageSwitcher;
use Hofff\Contao\LanguageRelations\News\Util\ContaoNewsUtil;
use Hofff\Contao\LanguageRelations\Relations;
use Hofff\Contao\LanguageRelations\Util\ContaoUtil;

/**
 * @author Oliver Hoff <oliver@hofff.com>
 */
class LanguageRelationsNews {

	/**
	 * @var Relations
	 */
	private static $relations;

	/**
	 * @return Relations
	 * @deprecated
	 */
	public static function getRelationsInstance() {
		isset(self::$relations) || self::$relations = new Relations(
			'tl_hofff_language_relations_news',
			'hofff_language_relations_news_item',
			'hofff_language_relations_news_relation'
		);
		return self::$relations;
	}

	/**
	 * @param array $items
	 * @param ModuleLanguageSwitcher $module
	 * @return array
	 */
	public function hookLanguageSwitcher(array $items, ModuleLanguageSwitcher $module) {
		$currentPage = $GLOBALS['objPage'];

		$currentNews = ContaoNewsUtil::findCurrentNews($currentPage->id);
		if(!$currentNews) {
			return $items;
		}

		$relatedNews = self::getRelationsInstance()->getRelations($currentNews);

		if($module->hofff_language_relations_hide_current) {
			$relatedNews[$currentPage->hofff_root_page_id] = $currentNews;
		}

		if(!$relatedNews) {
			return $items;
		}

		$this->prefetchModels($relatedNews);

		foreach($items as $rootPageID => &$item) {
			if(!isset($relatedNews[$rootPageID])) {
				continue;
			}

			$news = \NewsModel::findByPk($relatedNews[$rootPageID]);

			if(!ContaoUtil::isPublished($news)) {
				continue;
			}

			$archive = $news->getRelated('pid');
			if(!$archive->jumpTo || !ContaoUtil::isPublished($archive->getRelated('jumpTo'))) {
				continue;
			}

			$item['href']		= ContaoNewsUtil::getNewsURL($news);
			$item['pageTitle']	= strip_tags($news->headline);
		}
		unset($item);

		return $items;
	}

	/**
	 * @param array $relatedNews
	 * @return void
	 */
	protected function prefetchModels(array $relatedNews) {
		$archives = [];
		foreach(\NewsModel::findMultipleByIds($relatedNews) as $news) {
			$archives[] = $news->pid;
		}

		$pages = [];
		foreach(\NewsArchiveModel::findMultipleByIds($archives) as $archive) {
			$archive->jumpTo && $pages[] = $archive->jumpTo;
		}

		\PageModel::findMultipleByIds($pages);
	}

}

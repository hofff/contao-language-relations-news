<?php

declare(strict_types=1);

namespace Hofff\Contao\LanguageRelations\News;

use Contao\NewsArchiveModel;
use Contao\NewsModel;
use Contao\PageModel;
use Hofff\Contao\LanguageRelations\Module\ModuleLanguageSwitcher;
use Hofff\Contao\LanguageRelations\News\Util\ContaoNewsUtil;
use Hofff\Contao\LanguageRelations\Relations;
use Hofff\Contao\LanguageRelations\Util\ContaoUtil;
use function strip_tags;

class LanguageRelationsNews
{
    /** @var Relations */
    private static $relations;

    /**
     * @deprecated
     */
    public static function getRelationsInstance() : Relations
    {
        isset(self::$relations) || self::$relations = new Relations(
            'tl_hofff_language_relations_news',
            'hofff_language_relations_news_item',
            'hofff_language_relations_news_relation'
        );
        return self::$relations;
    }

    /**
     * @param string[][] $items
     *
     * @return string[][]
     */
    public function hookLanguageSwitcher(array $items, ModuleLanguageSwitcher $module) : array
    {
        $currentPage = $GLOBALS['objPage'];

        $currentNews = ContaoNewsUtil::findCurrentNews($currentPage->id);
        if (! $currentNews) {
            return $items;
        }

        $relatedNews                                   = self::getRelationsInstance()->getRelations($currentNews);
        $relatedNews[$currentPage->hofff_root_page_id] = $currentNews;

        ContaoNewsUtil::prefetchNewsModels($relatedNews);

        foreach ($items as $rootPageID => &$item) {
            if (! isset($relatedNews[$rootPageID])) {
                continue;
            }

            $news = NewsModel::findByPk($relatedNews[$rootPageID]);
            if (! ContaoUtil::isPublished($news)) {
                continue;
            }

            $archive = NewsArchiveModel::findByPk($news->pid);
            if (! $archive->jumpTo) {
                continue;
            }

            $page = PageModel::findByPk($archive->jumpTo);
            if (! ContaoUtil::isPublished($page)) {
                continue;
            }

            $item['href']      = ContaoNewsUtil::getNewsURL($news);
            $item['pageTitle'] = strip_tags($news->headline);
        }
        unset($item);

        return $items;
    }
}

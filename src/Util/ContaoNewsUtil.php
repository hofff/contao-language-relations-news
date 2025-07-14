<?php

declare(strict_types=1);

namespace Hofff\Contao\LanguageRelations\News\Util;

use Contao\Config;
use Contao\Input;
use Contao\Model\Collection;
use Contao\News;
use Contao\NewsArchiveModel;
use Contao\NewsModel;
use Contao\PageModel;
use Hofff\Contao\LanguageRelations\Util\QueryUtil;

use function array_values;

final class ContaoNewsUtil
{
    /** @SuppressWarnings(PHPMD.Superglobals) */
    public static function findCurrentNews(?int $jumpTo = null): ?int
    {
        if (isset($_GET['items'])) {
            $idOrAlias = Input::get('items', false, true);
        } elseif (isset($_GET['auto_item']) && Config::get('useAutoItem')) {
            $idOrAlias = Input::get('auto_item', false, true);
        } else {
            return null;
        }

        $sql    = '
SELECT
    news.id        AS news_id,
    archive.jumpTo AS archive_jump_to
FROM
    tl_news
    AS news
JOIN
    tl_news_archive
    AS archive
    ON archive.id = news.pid
WHERE
    news.id = ? OR news.alias = ?
';
        $result = QueryUtil::query(
            $sql,
            null,
            [$idOrAlias, $idOrAlias]
        );

        if (! $result->numRows) {
            return null;
        }

        if ($jumpTo === null || $jumpTo === (int) $result->archive_jump_to) {
            return (int) $result->news_id;
        }

        return null;
    }

    public static function getNewsURL(NewsModel $news): string
    {
        return News::generateNewsUrl($news);
    }

    /** @param int[] $ids */
    public static function prefetchNewsModels(array $ids): void
    {
        $archives   = [];
        $collection = NewsModel::findMultipleByIds(array_values($ids));
        if ($collection instanceof Collection) {
            foreach ($collection as $news) {
                $archives[] = $news->pid;
            }
        }

        $pages      = [];
        $collection = NewsArchiveModel::findMultipleByIds($archives);
        if ($collection instanceof Collection) {
            foreach ($collection as $archive) {
                $archive->jumpTo && $pages[] = $archive->jumpTo;
            }
        }

        PageModel::findMultipleByIds($pages);
    }
}

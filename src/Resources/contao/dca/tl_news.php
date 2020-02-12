<?php

declare(strict_types=1);

use Hofff\Contao\LanguageRelations\DCA\RelationsDCABuilder;
use Hofff\Contao\LanguageRelations\DCA\RelationsDCABuilderConfig;
use Hofff\Contao\LanguageRelations\Relations;
use Hofff\Contao\Selectri\Model\Tree\SQLAdjacencyTreeDataFactory;

call_user_func(static function () : void {
    $relations = new Relations(
        'tl_hofff_language_relations_news',
        'hofff_language_relations_news_item',
        'hofff_language_relations_news_relation'
    );

    $config = new RelationsDCABuilderConfig();
    $config->setRelations($relations);
    $config->setAggregateFieldName('pid');
    $config->setAggregateView('hofff_language_relations_news_aggregate');
    $config->setTreeView('hofff_language_relations_news_tree');
    $config->setSelectriDataFactoryConfiguratorCallback(
        static function (SQLAdjacencyTreeDataFactory $factory) : void {
            $factory->getConfig()->addColumns([ 'date', 'type' ]);
            $factory->getConfig()->setOrderByExpr('date DESC, title');
        }
    );
    $config->setSelectriNodeLabelTemplate('hofff_language_relations_news_node_label');

    $builder = new RelationsDCABuilder($config);
    $builder->build($GLOBALS['TL_DCA']['tl_news']);
});

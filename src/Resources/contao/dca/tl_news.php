<?php

declare(strict_types=1);

call_user_func(static function () : void {
    $relations = new Hofff\Contao\LanguageRelations\Relations(
        'tl_hofff_language_relations_news',
        'hofff_language_relations_news_item',
        'hofff_language_relations_news_relation'
    );

    $config = new Hofff\Contao\LanguageRelations\DCA\RelationsDCABuilderConfig();
    $config->setRelations($relations);
    $config->setAggregateFieldName('pid');
    $config->setAggregateView('hofff_language_relations_news_aggregate');
    $config->setTreeView('hofff_language_relations_news_tree');
    $config->setSelectriDataFactoryConfiguratorCallback(
        static function (Hofff\Contao\Selectri\Model\Tree\SQLAdjacencyTreeDataFactory $factory) : void {
            $factory->getConfig()->addColumns([ 'date', 'type' ]);
            $factory->getConfig()->setOrderByExpr('date DESC, title');
        }
    );
    $config->setSelectriNodeLabelTemplate('hofff_language_relations_news_node_label');

    $builder = new Hofff\Contao\LanguageRelations\DCA\RelationsDCABuilder($config);
    $builder->build($GLOBALS['TL_DCA']['tl_news']);
});

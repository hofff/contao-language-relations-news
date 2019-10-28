<?php

declare(strict_types=1);

use Contao\Database;

$db = Database::getInstance();

$db->query('DROP VIEW IF EXISTS hofff_language_relations_news_tree');
$db->query('DROP VIEW IF EXISTS hofff_language_relations_news_aggregate');
$db->query('DROP VIEW IF EXISTS hofff_language_relations_news_relation');
$db->query('DROP VIEW IF EXISTS hofff_language_relations_news_item');
$db->query('DROP VIEW IF EXISTS hofff_language_relations_news_archive');

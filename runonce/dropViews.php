<?php

Database::getInstance()->query('DROP VIEW IF EXISTS hofff_language_relations_news_tree');
Database::getInstance()->query('DROP VIEW IF EXISTS hofff_language_relations_news_aggregate');
Database::getInstance()->query('DROP VIEW IF EXISTS hofff_language_relations_news_relation');
Database::getInstance()->query('DROP VIEW IF EXISTS hofff_language_relations_news_item');

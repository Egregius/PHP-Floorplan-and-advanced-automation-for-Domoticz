<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Schema export handler
 *
 * @package PhpMyAdmin
 */
declare(strict_types=1);

use PhpMyAdmin\DatabaseInterface;
use PhpMyAdmin\Di\Container;
use PhpMyAdmin\Export;
use PhpMyAdmin\Relation;
use PhpMyAdmin\Util;

if (! defined('ROOT_PATH')) {
    define('ROOT_PATH', __DIR__ . DIRECTORY_SEPARATOR);
}

require_once ROOT_PATH . 'libraries/common.inc.php';

$container = Container::getDefaultContainer();

/** @var DatabaseInterface $dbi */
$dbi = $container->get(DatabaseInterface::class);

/**
 * get all variables needed for exporting relational schema
 * in $cfgRelation
 */
$relation = new Relation($dbi);
$cfgRelation = $relation->getRelationsParam();

if (! isset($_REQUEST['export_type'])) {
    Util::checkParameters(['export_type']);
}

/**
 * Include the appropriate Schema Class depending on $export_type
 * default is PDF
 */
$export = new Export($dbi);
$export->processExportSchema($_REQUEST['export_type']);

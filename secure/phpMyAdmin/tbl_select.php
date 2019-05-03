<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Handles table search tab
 *
 * display table search form, create SQL query from form data
 * and call Sql::executeQueryAndSendQueryResponse() to execute it
 *
 * @package PhpMyAdmin
 */
declare(strict_types=1);

use PhpMyAdmin\Controllers\Table\SearchController;
use PhpMyAdmin\Di\Container;
use PhpMyAdmin\Response;

if (! defined('ROOT_PATH')) {
    define('ROOT_PATH', __DIR__ . DIRECTORY_SEPARATOR);
}

require_once ROOT_PATH . 'libraries/common.inc.php';
require_once ROOT_PATH . 'libraries/tbl_common.inc.php';

$container = Container::getDefaultContainer();
$container->factory(SearchController::class);
$container->set(Response::class, Response::getInstance());
$container->alias('response', Response::class);

/* Define dependencies for the concerned controller */
$dependency_definitions = [
    'searchType' => 'normal',
    'url_query' => &$url_query
];

/** @var SearchController $controller */
$controller = $container->get(SearchController::class, $dependency_definitions);
$controller->indexAction();

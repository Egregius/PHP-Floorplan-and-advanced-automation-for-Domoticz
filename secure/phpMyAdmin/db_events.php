<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Events management.
 *
 * @package PhpMyAdmin
 */
declare(strict_types=1);

use PhpMyAdmin\Controllers\Database\EventsController;
use PhpMyAdmin\DatabaseInterface;
use PhpMyAdmin\Di\Container;
use PhpMyAdmin\Response;
use PhpMyAdmin\Url;
use PhpMyAdmin\Util;

if (! defined('ROOT_PATH')) {
    define('ROOT_PATH', __DIR__ . DIRECTORY_SEPARATOR);
}

require_once ROOT_PATH . 'libraries/common.inc.php';

$container = Container::getDefaultContainer();
$container->set(Response::class, Response::getInstance());

/** @var Response $response */
$response = $container->get(Response::class);

/** @var DatabaseInterface $dbi */
$dbi = $container->get(DatabaseInterface::class);

$_PMA_RTE = 'EVN';

$controller = new EventsController(
    $response,
    $dbi,
    $db
);

if (! $response->isAjax()) {
    /**
     * Displays the header and tabs
     */
    if (! empty($table) && in_array($table, $dbi->getTables($db))) {
        include_once ROOT_PATH . 'libraries/tbl_common.inc.php';
    } else {
        $table = '';
        include_once ROOT_PATH . 'libraries/db_common.inc.php';

        list(
            $tables,
            $num_tables,
            $total_num_tables,
            $sub_part,
            $is_show_stats,
            $db_is_system_schema,
            $tooltip_truename,
            $tooltip_aliasname,
            $pos
            ) = Util::getDbInfo($db, isset($sub_part) ? $sub_part : '');
    }
} else {
    /**
     * Since we did not include some libraries, we need
     * to manually select the required database and
     * create the missing $url_query variable
     */
    if (strlen($db) > 0) {
        $dbi->selectDb($db);
        if (! isset($url_query)) {
            $url_query = Url::getCommon(
                [
                    'db' => $db,
                    'table' => $table,
                ]
            );
        }
    }
}

$controller->index();

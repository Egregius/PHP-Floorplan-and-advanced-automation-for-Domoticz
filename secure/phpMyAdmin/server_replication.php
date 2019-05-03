<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Server replications
 *
 * @package PhpMyAdmin
 */
declare(strict_types=1);

use PhpMyAdmin\Controllers\Server\ReplicationController;
use PhpMyAdmin\DatabaseInterface;
use PhpMyAdmin\Di\Container;
use PhpMyAdmin\ReplicationGui;
use PhpMyAdmin\Response;

if (! defined('ROOT_PATH')) {
    define('ROOT_PATH', __DIR__ . DIRECTORY_SEPARATOR);
}

require_once ROOT_PATH . 'libraries/common.inc.php';
require_once ROOT_PATH . 'libraries/server_common.inc.php';
require_once ROOT_PATH . 'libraries/replication.inc.php';

$container = Container::getDefaultContainer();
$container->set(Response::class, Response::getInstance());

/** @var Response $response */
$response = $container->get(Response::class);

/** @var DatabaseInterface $dbi */
$dbi = $container->get(DatabaseInterface::class);

$controller = new ReplicationController(
    $response,
    $dbi
);

$header = $response->getHeader();
$scripts = $header->getScripts();
$scripts->addFile('server_privileges.js');
$scripts->addFile('replication.js');
$scripts->addFile('vendor/zxcvbn.js');

if (isset($_POST['url_params']) && is_array($_POST['url_params'])) {
    $GLOBALS['url_params'] = $_POST['url_params'];
}

if ($dbi->isSuperuser()) {
    $replicationGui = new ReplicationGui();
    $replicationGui->handleControlRequest();
}

$response->addHTML($controller->index([
    'mr_configure' => $_POST['mr_configure'] ?? null,
    'sl_configure' => $_POST['sl_configure'] ?? null,
    'repl_clear_scr' => $_POST['repl_clear_scr'] ?? null,
]));

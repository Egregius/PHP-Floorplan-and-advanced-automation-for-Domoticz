<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Server SQL executor
 *
 * @package PhpMyAdmin
 */
declare(strict_types=1);

use PhpMyAdmin\Controllers\Server\SqlController;
use PhpMyAdmin\DatabaseInterface;
use PhpMyAdmin\Di\Container;
use PhpMyAdmin\Response;

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

$controller = new SqlController(
    $response,
    $dbi
);

$header = $response->getHeader();
$scripts = $header->getScripts();
$scripts->addFile('makegrid.js');
$scripts->addFile('vendor/jquery/jquery.uitablefilter.js');
$scripts->addFile('sql.js');

$response->addHTML($controller->index());

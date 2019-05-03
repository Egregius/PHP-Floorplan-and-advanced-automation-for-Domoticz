<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Table export
 *
 * @package PhpMyAdmin
 */
declare(strict_types=1);

use PhpMyAdmin\Config\PageSettings;
use PhpMyAdmin\DatabaseInterface;
use PhpMyAdmin\Di\Container;
use PhpMyAdmin\Display\Export;
use PhpMyAdmin\Relation;
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

PageSettings::showGroup('Export');

$header = $response->getHeader();
$scripts = $header->getScripts();
$scripts->addFile('export.js');

// Get the relation settings
$relation = new Relation($dbi);
$cfgRelation = $relation->getRelationsParam();

$displayExport = new Export();

// handling export template actions
if (isset($_POST['templateAction']) && $cfgRelation['exporttemplateswork']) {
    $displayExport->handleTemplateActions($cfgRelation);
    exit;
}

/**
 * Gets tables information and displays top links
 */
require_once ROOT_PATH . 'libraries/tbl_common.inc.php';
$url_query .= '&amp;goto=tbl_export.php&amp;back=tbl_export.php';

// Dump of a table

$export_page_title = __('View dump (schema) of table');

// When we have some query, we need to remove LIMIT from that and possibly
// generate WHERE clause (if we are asked to export specific rows)

if (! empty($sql_query)) {
    $parser = new PhpMyAdmin\SqlParser\Parser($sql_query);

    if (! empty($parser->statements[0])
        && ($parser->statements[0] instanceof PhpMyAdmin\SqlParser\Statements\SelectStatement)
    ) {
        // Finding aliases and removing them, but we keep track of them to be
        // able to replace them in select expression too.
        $aliases = [];
        foreach ($parser->statements[0]->from as $from) {
            if (! empty($from->table) && ! empty($from->alias)) {
                $aliases[$from->alias] = $from->table;
                // We remove the alias of the table because they are going to
                // be replaced anyway.
                $from->alias = null;
                $from->expr = null; // Force rebuild.
            }
        }

        // Rebuilding the SELECT and FROM clauses.
        if (count($parser->statements[0]->from) > 0
            && count($parser->statements[0]->union) === 0
        ) {
            $replaces = [
                [
                    'FROM',
                    'FROM ' . PhpMyAdmin\SqlParser\Components\ExpressionArray::build(
                        $parser->statements[0]->from
                    ),
                ],
            ];
        }

        // Checking if the WHERE clause has to be replaced.
        if (! empty($where_clause) && is_array($where_clause)) {
            $replaces[] = [
                'WHERE',
                'WHERE (' . implode(') OR (', $where_clause) . ')',
            ];
        }

        // Preparing to remove the LIMIT clause.
        $replaces[] = [
            'LIMIT',
            '',
        ];

        // Replacing the clauses.
        $sql_query = PhpMyAdmin\SqlParser\Utils\Query::replaceClauses(
            $parser->statements[0],
            $parser->list,
            $replaces
        );

        // Removing the aliases by finding the alias followed by a dot.
        $tokens = PhpMyAdmin\SqlParser\Lexer::getTokens($sql_query);
        foreach ($aliases as $alias => $table) {
            $tokens = PhpMyAdmin\SqlParser\Utils\Tokens::replaceTokens(
                $tokens,
                [
                    [
                        'value_str' => $alias,
                    ],
                    [
                        'type' => PhpMyAdmin\SqlParser\Token::TYPE_OPERATOR,
                        'value_str' => '.',
                    ],
                ],
                [
                    new PhpMyAdmin\SqlParser\Token($table),
                    new PhpMyAdmin\SqlParser\Token('.', PhpMyAdmin\SqlParser\Token::TYPE_OPERATOR),
                ]
            );
        }
        $sql_query = PhpMyAdmin\SqlParser\TokensList::build($tokens);
    }

    echo PhpMyAdmin\Util::getMessage(PhpMyAdmin\Message::success());
}

if (! isset($sql_query)) {
    $sql_query = '';
}
if (! isset($num_tables)) {
    $num_tables = 0;
}
if (! isset($unlim_num_rows)) {
    $unlim_num_rows = 0;
}
if (! isset($multi_values)) {
    $multi_values = '';
}
$response = Response::getInstance();
$response->addHTML(
    $displayExport->getDisplay(
        'table',
        $db,
        $table,
        $sql_query,
        $num_tables,
        $unlim_num_rows,
        $multi_values
    )
);

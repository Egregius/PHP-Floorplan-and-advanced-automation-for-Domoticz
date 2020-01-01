<?php
/**
 * SQL executor
 *
 * @todo    we must handle the case if /sql is called directly with a query
 *          that returns 0 rows - to prevent cyclic redirects or includes
 * @package PhpMyAdmin
 */
declare(strict_types=1);

use PhpMyAdmin\CheckUserPrivileges;
use PhpMyAdmin\Config\PageSettings;
use PhpMyAdmin\Core;
use PhpMyAdmin\DatabaseInterface;
use PhpMyAdmin\Html\Generator;
use PhpMyAdmin\ParseAnalyze;
use PhpMyAdmin\Response;
use PhpMyAdmin\Sql;
use PhpMyAdmin\Url;
use PhpMyAdmin\Util;

if (! defined('PHPMYADMIN')) {
    exit;
}

global $cfg, $containerBuilder, $db, $display_query, $pmaThemeImage, $sql_query, $table, $message;
global $ajax_reload, $goto, $err_url, $find_real_end, $unlim_num_rows, $import_text, $disp_query;
global $extra_data, $message_to_show, $sql_data, $disp_message, $query_type, $selected, $complete_query;
global $is_gotofile, $back, $table_from_sql;

/** @var Response $response */
$response = $containerBuilder->get(Response::class);

/** @var DatabaseInterface $dbi */
$dbi = $containerBuilder->get(DatabaseInterface::class);

/** @var CheckUserPrivileges $checkUserPrivileges */
$checkUserPrivileges = $containerBuilder->get('check_user_privileges');
$checkUserPrivileges->getPrivileges();

PageSettings::showGroup('Browse');

$header = $response->getHeader();
$scripts = $header->getScripts();
$scripts->addFile('vendor/jquery/jquery.uitablefilter.js');
$scripts->addFile('table/change.js');
$scripts->addFile('indexes.js');
$scripts->addFile('gis_data_editor.js');
$scripts->addFile('multi_column_sort.js');

/** @var Sql $sql */
$sql = $containerBuilder->get('sql');

/**
 * Set ajax_reload in the response if it was already set
 */
if (isset($ajax_reload) && $ajax_reload['reload'] === true) {
    $response->addJSON('ajax_reload', $ajax_reload);
}

/**
 * Defines the url to return to in case of error in a sql statement
 */
$is_gotofile  = true;
if (empty($goto)) {
    if (empty($table)) {
        $goto = Util::getScriptNameForOption(
            $cfg['DefaultTabDatabase'],
            'database'
        );
    } else {
        $goto = Util::getScriptNameForOption(
            $cfg['DefaultTabTable'],
            'table'
        );
    }
}

if (! isset($err_url)) {
    $err_url = ! empty($back) ? $back : $goto;
    $err_url .= Url::getCommon(
        ['db' => $GLOBALS['db']],
        strpos($err_url, '?') === false ? '?' : '&'
    );
    if ((mb_strpos(' ' . $err_url, 'db_') !== 1 || mb_strpos($err_url, '?route=/database/') === false)
        && strlen($table) > 0
    ) {
        $err_url .= '&amp;table=' . urlencode($table);
    }
}

// Coming from a bookmark dialog
if (isset($_POST['bkm_fields']['bkm_sql_query'])) {
    $sql_query = $_POST['bkm_fields']['bkm_sql_query'];
} elseif (isset($_POST['sql_query'])) {
    $sql_query = $_POST['sql_query'];
} elseif (isset($_GET['sql_query'], $_GET['sql_signature'])) {
    if (Core::checkSqlQuerySignature($_GET['sql_query'], $_GET['sql_signature'])) {
        $sql_query = $_GET['sql_query'];
    }
}

// This one is just to fill $db
if (isset($_POST['bkm_fields']['bkm_database'])) {
    $db = $_POST['bkm_fields']['bkm_database'];
}

// During grid edit, if we have a relational field, show the dropdown for it.
if (isset($_POST['get_relational_values'])
    && $_POST['get_relational_values'] == true
) {
    $sql->getRelationalValues($db, $table);
    // script has exited at this point
}

// Just like above, find possible values for enum fields during grid edit.
if (isset($_POST['get_enum_values']) && $_POST['get_enum_values'] == true) {
    $sql->getEnumOrSetValues($db, $table, 'enum');
    // script has exited at this point
}


// Find possible values for set fields during grid edit.
if (isset($_POST['get_set_values']) && $_POST['get_set_values'] == true) {
    $sql->getEnumOrSetValues($db, $table, 'set');
    // script has exited at this point
}

if (isset($_GET['get_default_fk_check_value'])
    && $_GET['get_default_fk_check_value'] == true
) {
    $response = Response::getInstance();
    $response->addJSON(
        'default_fk_check_value',
        Util::isForeignKeyCheck()
    );
    exit;
}

/**
 * Check ajax request to set the column order and visibility
 */
if (isset($_POST['set_col_prefs']) && $_POST['set_col_prefs'] == true) {
    $sql->setColumnOrderOrVisibility($table, $db);
    // script has exited at this point
}

// Default to browse if no query set and we have table
// (needed for browsing from DefaultTabTable)
if (empty($sql_query) && strlen($table) > 0 && strlen($db) > 0) {
    $sql_query = $sql->getDefaultSqlQueryForBrowse($db, $table);

    // set $goto to what will be displayed if query returns 0 rows
    $goto = '';
} else {
    // Now we can check the parameters
    Util::checkParameters(['sql_query']);
}

/**
 * Parse and analyze the query
 */
[
    $analyzed_sql_results,
    $db,
    $table_from_sql,
] = ParseAnalyze::sqlQuery($sql_query, $db);
// @todo: possibly refactor
extract($analyzed_sql_results);

if ($table != $table_from_sql && ! empty($table_from_sql)) {
    $table = $table_from_sql;
}


/**
 * Check rights in case of DROP DATABASE
 *
 * This test may be bypassed if $is_js_confirmed = 1 (already checked with js)
 * but since a malicious user may pass this variable by url/form, we don't take
 * into account this case.
 */
if ($sql->hasNoRightsToDropDatabase(
    $analyzed_sql_results,
    $cfg['AllowUserDropDatabase'],
    $dbi->isSuperuser()
)) {
    Generator::mysqlDie(
        __('"DROP DATABASE" statements are disabled.'),
        '',
        false,
        $err_url
    );
} // end if

/**
 * Need to find the real end of rows?
 */
if (isset($find_real_end) && $find_real_end) {
    $unlim_num_rows = $sql->findRealEndOfRows($db, $table);
}


/**
 * Bookmark add
 */
if (isset($_POST['store_bkm'])) {
    $sql->addBookmark($goto);
    // script has exited at this point
} // end if


/**
 * Sets or modifies the $goto variable if required
 */
if ($goto === Url::getFromRoute('/sql')) {
    $is_gotofile = false;
    $goto = Url::getFromRoute('/sql', [
        'db' => $db,
        'table' => $table,
        'sql_query' => $sql_query,
    ]);
}

$sql->executeQueryAndSendQueryResponse(
    $analyzed_sql_results, // analyzed_sql_results
    $is_gotofile, // is_gotofile
    $db, // db
    $table, // table
    $find_real_end ?? null, // find_real_end
    $import_text ?? null, // sql_query_for_bookmark
    $extra_data ?? null, // extra_data
    $message_to_show ?? null, // message_to_show
    $message ?? null, // message
    $sql_data ?? null, // sql_data
    $goto, // goto
    $pmaThemeImage, // pmaThemeImage
    isset($disp_query) ? $display_query : null, // disp_query
    $disp_message ?? null, // disp_message
    $query_type ?? null, // query_type
    $sql_query, // sql_query
    $selected ?? null, // selectedTables
    $complete_query ?? null // complete_query
);

<?php
/**
 * @package PhpMyAdmin\Controllers\Table
 */
declare(strict_types=1);

namespace PhpMyAdmin\Controllers\Table;

use PhpMyAdmin\Config;
use PhpMyAdmin\CreateAddField;
use PhpMyAdmin\DatabaseInterface;
use PhpMyAdmin\Html\Generator;
use PhpMyAdmin\Message;
use PhpMyAdmin\Response;
use PhpMyAdmin\Template;
use PhpMyAdmin\Transformations;
use PhpMyAdmin\Url;
use PhpMyAdmin\Util;

/**
 * Displays add field form and handles it.
 *
 * @package PhpMyAdmin\Controllers\Table
 */
class AddFieldController extends AbstractController
{
    /** @var Transformations */
    private $transformations;

    /** @var Config */
    private $config;

    /**
     * @param Response          $response        A Response instance.
     * @param DatabaseInterface $dbi             A DatabaseInterface instance.
     * @param Template          $template        A Template instance.
     * @param string            $db              Database name.
     * @param string            $table           Table name.
     * @param Transformations   $transformations A Transformations instance.
     * @param Config            $config          A Config instance.
     */
    public function __construct(
        $response,
        $dbi,
        Template $template,
        $db,
        $table,
        Transformations $transformations,
        Config $config
    ) {
        parent::__construct($response, $dbi, $template, $db, $table);
        $this->transformations = $transformations;
        $this->config = $config;
    }

    /**
     * @return void
     */
    public function index(): void
    {
        global $err_url, $message, $action, $active_page, $sql_query;
        global $abort, $num_fields, $regenerate, $result, $db, $table;

        $header = $this->response->getHeader();
        $scripts = $header->getScripts();
        $scripts->addFile('table/structure.js');

        // Check parameters
        Util::checkParameters(['db', 'table']);

        $cfg = $this->config->settings;

        /**
         * Defines the url to return to in case of error in a sql statement
         */
        $err_url = Url::getFromRoute('/table/sql', [
            'db' => $db,
            'table' => $table,
        ]);

        /**
         * The form used to define the field to add has been submitted
         */
        $abort = false;

        // check number of fields to be created
        if (isset($_POST['submit_num_fields'])) {
            if (isset($_POST['orig_after_field'])) {
                $_POST['after_field'] = $_POST['orig_after_field'];
            }
            if (isset($_POST['orig_field_where'])) {
                $_POST['field_where'] = $_POST['orig_field_where'];
            }
            $num_fields = min(
                intval($_POST['orig_num_fields']) + intval($_POST['added_fields']),
                4096
            );
            $regenerate = true;
        } elseif (isset($_POST['num_fields']) && intval($_POST['num_fields']) > 0) {
            $num_fields = min(4096, intval($_POST['num_fields']));
        } else {
            $num_fields = 1;
        }

        if (isset($_POST['do_save_data'])) {
            // avoid an incorrect calling of PMA_updateColumns() via
            // /table/structure below
            unset($_POST['do_save_data']);

            $createAddField = new CreateAddField($this->dbi);

            [$result, $sql_query] = $createAddField->tryColumnCreationQuery($db, $table, $err_url);

            if ($result === true) {
                // Update comment table for mime types [MIME]
                if (isset($_POST['field_mimetype'])
                    && is_array($_POST['field_mimetype'])
                    && $cfg['BrowseMIME']
                ) {
                    foreach ($_POST['field_mimetype'] as $fieldindex => $mimetype) {
                        if (isset($_POST['field_name'][$fieldindex])
                            && strlen($_POST['field_name'][$fieldindex]) > 0
                        ) {
                            $this->transformations->setMime(
                                $db,
                                $table,
                                $_POST['field_name'][$fieldindex],
                                $mimetype,
                                $_POST['field_transformation'][$fieldindex],
                                $_POST['field_transformation_options'][$fieldindex],
                                $_POST['field_input_transformation'][$fieldindex],
                                $_POST['field_input_transformation_options'][$fieldindex]
                            );
                        }
                    }
                }

                // Go back to the structure sub-page
                $message = Message::success(
                    __('Table %1$s has been altered successfully.')
                );
                $message->addParam($table);
                $this->response->addJSON(
                    'message',
                    Generator::getMessage($message, $sql_query, 'success')
                );
                return;
            } else {
                $error_message_html = Generator::mysqlDie(
                    '',
                    '',
                    false,
                    $err_url,
                    false
                );
                $this->response->addHTML($error_message_html ?? '');
                $this->response->setRequestStatus(false);
                return;
            }
        }

        /**
         * Displays the form used to define the new field
         */
        if ($abort === false) {
            /**
             * Gets tables information
             */
            include_once ROOT_PATH . 'libraries/tbl_common.inc.php';

            $active_page = Url::getFromRoute('/table/structure');
            /**
             * Display the form
             */
            $action = Url::getFromRoute('/table/add-field');
            include_once ROOT_PATH . 'libraries/tbl_columns_definition_form.inc.php';
        }
    }
}

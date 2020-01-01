<?php
/**
 * @package PhpMyAdmin\Controllers\Table
 */
declare(strict_types=1);

namespace PhpMyAdmin\Controllers\Table;

use PhpMyAdmin\Core;
use PhpMyAdmin\DatabaseInterface;
use PhpMyAdmin\Gis\GisVisualization;
use PhpMyAdmin\Message;
use PhpMyAdmin\Response;
use PhpMyAdmin\Template;
use PhpMyAdmin\Url;
use PhpMyAdmin\Util;

/**
 * Handles creation of the GIS visualizations.
 *
 * @package PhpMyAdmin\Controllers\Table
 */
final class GisVisualizationController extends AbstractController
{
    /**
     * @var GisVisualization
     */
    protected $visualization;

    /**
     * @param Response          $response Response object
     * @param DatabaseInterface $dbi      DatabaseInterface object
     * @param Template          $template Template object
     * @param string            $db       Database name
     * @param string            $table    Table name
     */
    public function __construct(
        $response,
        $dbi,
        Template $template,
        $db,
        $table
    ) {
        parent::__construct($response, $dbi, $template, $db, $table);
    }

    /**
     * @return void
     */
    public function index(): void
    {
        global $cfg, $url_params;

        require_once ROOT_PATH . 'libraries/db_common.inc.php';

        // SQL query for retrieving GIS data
        $sqlQuery = '';
        if (isset($_GET['sql_query'], $_GET['sql_signature'])) {
            if (Core::checkSqlQuerySignature($_GET['sql_query'], $_GET['sql_signature'])) {
                $sqlQuery = $_GET['sql_query'];
            }
        } elseif (isset($_POST['sql_query'])) {
            $sqlQuery = $_POST['sql_query'];
        }

        // Throw error if no sql query is set
        if ($sqlQuery == '') {
            $this->response->setRequestStatus(false);
            $this->response->addHTML(
                Message::error(__('No SQL query was set to fetch data.'))
            );
            return;
        }

        // Execute the query and return the result
        $result = $this->dbi->tryQuery($sqlQuery);
        // Get the meta data of results
        $meta = $this->dbi->getFieldsMeta($result);

        // Find the candidate fields for label column and spatial column
        $labelCandidates = [];
        $spatialCandidates = [];
        foreach ($meta as $column_meta) {
            if ($column_meta->type == 'geometry') {
                $spatialCandidates[] = $column_meta->name;
            } else {
                $labelCandidates[] = $column_meta->name;
            }
        }

        // Get settings if any posted
        $visualizationSettings = [];
        if (Core::isValid($_POST['visualizationSettings'], 'array')) {
            $visualizationSettings = $_POST['visualizationSettings'];
        }

        // Check mysql version
        $visualizationSettings['mysqlVersion'] = $this->dbi->getVersion();

        if (! isset($visualizationSettings['labelColumn'])
            && isset($labelCandidates[0])
        ) {
            $visualizationSettings['labelColumn'] = '';
        }

        // If spatial column is not set, use first geometric column as spatial column
        if (! isset($visualizationSettings['spatialColumn'])) {
            $visualizationSettings['spatialColumn'] = $spatialCandidates[0];
        }

        // Convert geometric columns from bytes to text.
        $pos = $_GET['pos'] ?? $_SESSION['tmpval']['pos'];
        if (isset($_GET['session_max_rows'])) {
            $rows = $_GET['session_max_rows'];
        } else {
            if ($_SESSION['tmpval']['max_rows'] != 'all') {
                $rows = $_SESSION['tmpval']['max_rows'];
            } else {
                $rows = $GLOBALS['cfg']['MaxRows'];
            }
        }
        $this->visualization = GisVisualization::get(
            $sqlQuery,
            $visualizationSettings,
            $rows,
            $pos
        );

        if (isset($_GET['saveToFile'])) {
            $this->saveToFile($visualizationSettings['spatialColumn'], $_GET['fileFormat']);
            return;
        }

        $this->response->getHeader()->getScripts()->addFiles(
            [
                'vendor/openlayers/OpenLayers.js',
                'vendor/jquery/jquery.svg.js',
                'table/gis_visualization.js',
            ]
        );

        // If all the rows contain SRID, use OpenStreetMaps on the initial loading.
        if (! isset($_POST['displayVisualization'])) {
            if ($this->visualization->hasSrid()) {
                $visualizationSettings['choice'] = 'useBaseLayer';
            } else {
                unset($visualizationSettings['choice']);
            }
        }

        $this->visualization->setUserSpecifiedSettings($visualizationSettings);
        if ($visualizationSettings != null) {
            foreach ($this->visualization->getSettings() as $setting => $val) {
                if (! isset($visualizationSettings[$setting])) {
                    $visualizationSettings[$setting] = $val;
                }
            }
        }

        /**
         * Displays the page
         */
        $url_params['goto'] = Util::getScriptNameForOption(
            $cfg['DefaultTabDatabase'],
            'database'
        );
        $url_params['back'] = Url::getFromRoute('/sql');
        $url_params['sql_query'] = $sqlQuery;
        $downloadUrl = Url::getFromRoute('/table/gis-visualization', array_merge(
            $url_params,
            [
                'saveToFile' => true,
                'session_max_rows' => $rows,
                'pos' => $pos,
            ]
        ));
        $html = $this->template->render('table/gis_visualization/gis_visualization', [
            'url_params' => $url_params,
            'download_url' => $downloadUrl,
            'label_candidates' => $labelCandidates,
            'spatial_candidates' => $spatialCandidates,
            'visualization_settings' => $visualizationSettings,
            'sql_query' => $sqlQuery,
            'visualization' => $this->visualization->toImage('svg'),
            'draw_ol' => $this->visualization->asOl(),
            'pma_theme_image' => $GLOBALS['pmaThemeImage'],
        ]);

        $this->response->addHTML($html);
    }

    /**
     * @param string $filename File name
     * @param string $format   Save format
     *
     * @return void
     */
    private function saveToFile(string $filename, string $format): void
    {
        $this->response->disable();
        $this->visualization->toFile($filename, $format);
    }
}

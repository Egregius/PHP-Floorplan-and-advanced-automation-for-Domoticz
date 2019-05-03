<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* server/status/monitor/index.twig */
class __TwigTemplate_2ca718c5583593a4bb71b5ca9ee94636816780d722edc11584c08ef8fb8bae66 extends \Twig\Template
{
    private $source;

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'content' => [$this, 'block_content'],
        ];
    }

    protected function doGetParent(array $context)
    {
        // line 1
        return "server/status/base.twig";
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        // line 2
        $context["active"] = "monitor";
        // line 1
        $this->parent = $this->loadTemplate("server/status/base.twig", "server/status/monitor/index.twig", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = [])
    {
        // line 4
        echo "
<div class=\"tabLinks\">
  <a href=\"#pauseCharts\">
    ";
        // line 7
        echo PhpMyAdmin\Util::getImage("play");
        echo "
    ";
        // line 8
        echo _gettext("Start Monitor");
        // line 9
        echo "  </a>
  <a href=\"#settingsPopup\" class=\"popupLink\">
    ";
        // line 11
        echo PhpMyAdmin\Util::getImage("s_cog");
        echo "
    ";
        // line 12
        echo _gettext("Settings");
        // line 13
        echo "  </a>
  <a href=\"#monitorInstructionsDialog\">
    ";
        // line 15
        echo PhpMyAdmin\Util::getImage("b_help");
        echo "
    ";
        // line 16
        echo _gettext("Instructions/Setup");
        // line 17
        echo "  </a>
  <a href=\"#endChartEditMode\" class=\"hide\">
    ";
        // line 19
        echo PhpMyAdmin\Util::getImage("s_okay");
        echo "
    ";
        // line 20
        echo _gettext("Done dragging (rearranging) charts");
        // line 21
        echo "  </a>
</div>

<div class=\"popupContent settingsPopup\">
  <a href=\"#addNewChart\">
    ";
        // line 26
        echo PhpMyAdmin\Util::getImage("b_chart");
        echo "
    ";
        // line 27
        echo _gettext("Add chart");
        // line 28
        echo "  </a>
  <a href=\"#rearrangeCharts\">
    ";
        // line 30
        echo PhpMyAdmin\Util::getImage("b_tblops");
        echo "
    ";
        // line 31
        echo _gettext("Enable charts dragging");
        // line 32
        echo "  </a>
  <div class=\"clearfloat paddingtop\"></div>

  <div class=\"floatleft\">
    ";
        // line 36
        echo _gettext("Refresh rate");
        // line 37
        echo "    <br>
    <select id=\"id_gridChartRefresh\" class=\"refreshRate\" name=\"gridChartRefresh\">
      ";
        // line 39
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable([0 => 2, 1 => 3, 2 => 4, 3 => 5, 4 => 10, 5 => 20, 6 => 40, 7 => 60, 8 => 120, 9 => 300, 10 => 600, 11 => 1200]);
        foreach ($context['_seq'] as $context["_key"] => $context["rate"]) {
            // line 40
            echo "        <option value=\"";
            echo twig_escape_filter($this->env, $context["rate"], "html", null, true);
            echo "\"";
            echo ((($context["rate"] == 5)) ? (" selected") : (""));
            echo ">
          ";
            // line 41
            if (($context["rate"] < 60)) {
                // line 42
                echo "            ";
                if (($context["rate"] == 1)) {
                    // line 43
                    echo "              ";
                    echo twig_escape_filter($this->env, sprintf(_gettext("%d second"), $context["rate"]), "html", null, true);
                    echo "
            ";
                } else {
                    // line 45
                    echo "              ";
                    echo twig_escape_filter($this->env, sprintf(_gettext("%d seconds"), $context["rate"]), "html", null, true);
                    echo "
            ";
                }
                // line 47
                echo "          ";
            } else {
                // line 48
                echo "            ";
                if ((($context["rate"] / 60) == 1)) {
                    // line 49
                    echo "              ";
                    echo twig_escape_filter($this->env, sprintf(_gettext("%d minute"), ($context["rate"] / 60)), "html", null, true);
                    echo "
            ";
                } else {
                    // line 51
                    echo "              ";
                    echo twig_escape_filter($this->env, sprintf(_gettext("%d minutes"), ($context["rate"] / 60)), "html", null, true);
                    echo "
            ";
                }
                // line 53
                echo "          ";
            }
            // line 54
            echo "        </option>
      ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['rate'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 56
        echo "    </select>
    <br>
  </div>

  <div class=\"floatleft\">
    ";
        // line 61
        echo _gettext("Chart columns");
        // line 62
        echo "    <br>
    <select name=\"chartColumns\">
      <option>1</option>
      <option>2</option>
      <option>3</option>
      <option>4</option>
      <option>5</option>
      <option>6</option>
    </select>
  </div>

  <div class=\"clearfloat paddingtop\">
    <strong>";
        // line 74
        echo _gettext("Chart arrangement");
        echo "</strong>
    ";
        // line 75
        echo PhpMyAdmin\Util::showHint(_gettext("The arrangement of the charts is stored to the browsers local storage. You may want to export it if you have a complicated set up."));
        echo "
    <br>
    <a class=\"ajax\" href=\"#importMonitorConfig\">
      ";
        // line 78
        echo _gettext("Import");
        // line 79
        echo "    </a> -
    <a class=\"disableAjax\" href=\"#exportMonitorConfig\">
      ";
        // line 81
        echo _gettext("Export");
        // line 82
        echo "    </a> -
    <a href=\"#clearMonitorConfig\">
      ";
        // line 84
        echo _gettext("Reset to default");
        // line 85
        echo "    </a>
  </div>
</div>

<div id=\"monitorInstructionsDialog\" title=\"";
        // line 89
        echo _gettext("Monitor Instructions");
        echo "\" class=\"hide\">
  <p>
    ";
        // line 91
        echo _gettext("The phpMyAdmin Monitor can assist you in optimizing the server configuration and track down time intensive queries. For the latter you will need to set log_output to 'TABLE' and have either the slow_query_log or general_log enabled. Note however, that the general_log produces a lot of data and increases server load by up to 15%.");
        // line 94
        echo "  </p>
  <img class=\"ajaxIcon\" src=\"";
        // line 95
        echo twig_escape_filter($this->env, ($context["image_path"] ?? null), "html", null, true);
        echo "ajax_clock_small.gif\" alt=\"";
        echo _gettext("Loading…");
        echo "\">

  <div class=\"ajaxContent\"></div>
  <br>

  <div class=\"monitorUse hide\">
    <p><strong>";
        // line 101
        echo _gettext("Using the monitor:");
        echo "</strong></p>
    <p>
      ";
        // line 103
        echo _gettext("Your browser will refresh all displayed charts in a regular interval. You may add charts and change the refresh rate under 'Settings', or remove any chart using the cog icon on each respective chart.");
        // line 106
        echo "    </p>
    <p>
      ";
        // line 108
        echo _gettext("To display queries from the logs, select the relevant time span on any chart by holding down the left mouse button and panning over the chart. Once confirmed, this will load a table of grouped queries, there you may click on any occurring SELECT statements to further analyze them.");
        // line 111
        echo "    </p>
    <p>
      ";
        // line 113
        echo PhpMyAdmin\Util::getImage("s_attention");
        echo "
      <strong>";
        // line 114
        echo _gettext("Please note:");
        echo "</strong>
    </p>
    <p>
      ";
        // line 117
        echo _gettext("Enabling the general_log may increase the server load by 5-15%. Also be aware that generating statistics from the logs is a load intensive task, so it is advisable to select only a small time span and to disable the general_log and empty its table once monitoring is not required any more.");
        // line 120
        echo "    </p>
  </div>
</div>

<div id=\"addChartDialog\" title=\"";
        // line 124
        echo _gettext("Add chart");
        echo "\" class=\"hide\">
  <div id=\"tabGridVariables\">
    <p>
      <input type=\"text\" name=\"chartTitle\" value=\"";
        // line 127
        echo _gettext("Chart Title");
        echo "\">
    </p>
    <input type=\"radio\" name=\"chartType\" value=\"preset\" id=\"chartPreset\">

    <label for=\"chartPreset\">";
        // line 131
        echo _gettext("Preset chart");
        echo "</label>
    <select name=\"presetCharts\"></select>
    <br>

    <input type=\"radio\" name=\"chartType\" value=\"variable\" id=\"chartStatusVar\" checked=\"checked\">
    <label for=\"chartStatusVar\">
      ";
        // line 137
        echo _gettext("Status variable(s)");
        // line 138
        echo "    </label>
    <br>

    <div id=\"chartVariableSettings\">
      <label for=\"chartSeries\">";
        // line 142
        echo _gettext("Select series:");
        echo "</label>
      <br>
      <select id=\"chartSeries\" name=\"varChartList\" size=\"1\">
        <option>";
        // line 145
        echo _gettext("Commonly monitored");
        echo "</option>
        <option>Processes</option>
        <option>Questions</option>
        <option>Connections</option>
        <option>Bytes_sent</option>
        <option>Bytes_received</option>
        <option>Threads_connected</option>
        <option>Created_tmp_disk_tables</option>
        <option>Handler_read_first</option>
        <option>Innodb_buffer_pool_wait_free</option>
        <option>Key_reads</option>
        <option>Open_tables</option>
        <option>Select_full_join</option>
        <option>Slow_queries</option>
      </select>
      <br>

      <label for=\"variableInput\">
        ";
        // line 163
        echo _gettext("or type variable name:");
        // line 164
        echo "      </label>
      <input type=\"text\" name=\"variableInput\" id=\"variableInput\">
      <br>

      <input type=\"checkbox\" name=\"differentialValue\" id=\"differentialValue\" value=\"differential\" checked=\"checked\">
      <label for=\"differentialValue\">
        ";
        // line 170
        echo _gettext("Display as differential value");
        // line 171
        echo "      </label>
      <br>

      <input type=\"checkbox\" id=\"useDivisor\" name=\"useDivisor\" value=\"1\">
      <label for=\"useDivisor\">";
        // line 175
        echo _gettext("Apply a divisor");
        echo "</label>

      <span class=\"divisorInput hide\">
        <input type=\"text\" name=\"valueDivisor\" size=\"4\" value=\"1\">
        (<a href=\"#kibDivisor\">";
        // line 179
        echo _gettext("KiB");
        echo "</a>,
        <a href=\"#mibDivisor\">";
        // line 180
        echo _gettext("MiB");
        echo "</a>)
      </span>
      <br>

      <input type=\"checkbox\" id=\"useUnit\" name=\"useUnit\" value=\"1\">
      <label for=\"useUnit\">
        ";
        // line 186
        echo _gettext("Append unit to data values");
        // line 187
        echo "      </label>
      <span class=\"unitInput hide\">
        <input type=\"text\" name=\"valueUnit\" size=\"4\" value=\"\">
      </span>

      <p>
        <a href=\"#submitAddSeries\">
          <strong>";
        // line 194
        echo _gettext("Add this series");
        echo "</strong>
        </a>
        <span id=\"clearSeriesLink\" class=\"hide\">
          | <a href=\"#submitClearSeries\">";
        // line 197
        echo _gettext("Clear series");
        echo "</a>
        </span>
      </p>

      ";
        // line 201
        echo _gettext("Series in chart:");
        // line 202
        echo "      <br>
      <span id=\"seriesPreview\">
        <em>";
        // line 204
        echo _gettext("None");
        echo "</em>
      </span>
    </div>
  </div>
</div>

<div id=\"logAnalyseDialog\" title=\"";
        // line 210
        echo _gettext("Log statistics");
        echo "\" class=\"hide\">
  <p>
    ";
        // line 212
        echo _gettext("Selected time range:");
        // line 213
        echo "    <input type=\"text\" name=\"dateStart\" class=\"datetimefield\" value=\"\">
    -
    <input type=\"text\" name=\"dateEnd\" class=\"datetimefield\" value=\"\">
  </p>

  <input type=\"checkbox\" id=\"limitTypes\" value=\"1\" checked=\"checked\">
  <label for=\"limitTypes\">
    ";
        // line 220
        echo _gettext("Only retrieve SELECT,INSERT,UPDATE and DELETE Statements");
        // line 221
        echo "  </label>
  <br>

  <input type=\"checkbox\" id=\"removeVariables\" value=\"1\" checked=\"checked\">
  <label for=\"removeVariables\">
    ";
        // line 226
        echo _gettext("Remove variable data in INSERT statements for better grouping");
        // line 227
        echo "  </label>

  <p>
    ";
        // line 230
        echo _gettext("Choose from which log you want the statistics to be generated from.");
        // line 231
        echo "  </p>
  <p>
    ";
        // line 233
        echo _gettext("Results are grouped by query text.");
        // line 234
        echo "  </p>
</div>

<div id=\"queryAnalyzerDialog\" title=\"";
        // line 237
        echo _gettext("Query analyzer");
        echo "\" class=\"hide\">
  <textarea id=\"sqlquery\"></textarea>
  <br>
  <div class=\"placeHolder\"></div>
</div>

<table class=\"clearfloat tdblock\" id=\"chartGrid\"></table>
<div id=\"logTable\"><br></div>

<script type=\"text/javascript\">
  variableNames = [
    ";
        // line 248
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["javascript_variable_names"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["variable_name"]) {
            // line 249
            echo "      \"";
            echo twig_escape_filter($this->env, twig_escape_filter($this->env, $context["variable_name"], "js"), "html", null, true);
            echo "\",
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['variable_name'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 251
        echo "  ];
</script>

<form id=\"js_data\" class=\"hide\">
  ";
        // line 255
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["form"] ?? null));
        foreach ($context['_seq'] as $context["name"] => $context["value"]) {
            // line 256
            echo "    <input type=\"hidden\" name=\"";
            echo twig_escape_filter($this->env, $context["name"], "html", null, true);
            echo "\" value=\"";
            echo twig_escape_filter($this->env, $context["value"], "html", null, true);
            echo "\">
  ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['name'], $context['value'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 258
        echo "</form>

<div id=\"profiling_docu\" class=\"hide\">
  ";
        // line 261
        echo PhpMyAdmin\Util::showMySQLDocu("general-thread-states");
        echo "
</div>

<div id=\"explain_docu\" class=\"hide\">
  ";
        // line 265
        echo PhpMyAdmin\Util::showMySQLDocu("explain-output");
        echo "
</div>

";
    }

    public function getTemplateName()
    {
        return "server/status/monitor/index.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  530 => 265,  523 => 261,  518 => 258,  507 => 256,  503 => 255,  497 => 251,  488 => 249,  484 => 248,  470 => 237,  465 => 234,  463 => 233,  459 => 231,  457 => 230,  452 => 227,  450 => 226,  443 => 221,  441 => 220,  432 => 213,  430 => 212,  425 => 210,  416 => 204,  412 => 202,  410 => 201,  403 => 197,  397 => 194,  388 => 187,  386 => 186,  377 => 180,  373 => 179,  366 => 175,  360 => 171,  358 => 170,  350 => 164,  348 => 163,  327 => 145,  321 => 142,  315 => 138,  313 => 137,  304 => 131,  297 => 127,  291 => 124,  285 => 120,  283 => 117,  277 => 114,  273 => 113,  269 => 111,  267 => 108,  263 => 106,  261 => 103,  256 => 101,  245 => 95,  242 => 94,  240 => 91,  235 => 89,  229 => 85,  227 => 84,  223 => 82,  221 => 81,  217 => 79,  215 => 78,  209 => 75,  205 => 74,  191 => 62,  189 => 61,  182 => 56,  175 => 54,  172 => 53,  166 => 51,  160 => 49,  157 => 48,  154 => 47,  148 => 45,  142 => 43,  139 => 42,  137 => 41,  130 => 40,  126 => 39,  122 => 37,  120 => 36,  114 => 32,  112 => 31,  108 => 30,  104 => 28,  102 => 27,  98 => 26,  91 => 21,  89 => 20,  85 => 19,  81 => 17,  79 => 16,  75 => 15,  71 => 13,  69 => 12,  65 => 11,  61 => 9,  59 => 8,  55 => 7,  50 => 4,  47 => 3,  42 => 1,  40 => 2,  34 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "server/status/monitor/index.twig", "/var/www/home.egregius.be/secure/phpMyAdmin/templates/server/status/monitor/index.twig");
    }
}

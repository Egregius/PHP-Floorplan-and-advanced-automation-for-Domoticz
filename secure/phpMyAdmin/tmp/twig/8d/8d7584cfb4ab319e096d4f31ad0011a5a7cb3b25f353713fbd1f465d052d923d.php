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

/* server/databases/index.twig */
class __TwigTemplate_87e1e8902095be6dd6208b3feaa84d5f3ea02a43099afaf745a3018d1a8b2db4 extends \Twig\Template
{
    private $source;

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        // line 1
        echo "<h2>
  ";
        // line 2
        echo PhpMyAdmin\Util::getImage("s_db");
        echo "
  ";
        // line 3
        if (($context["has_statistics"] ?? null)) {
            // line 4
            echo "    ";
            echo _gettext("Databases statistics");
            // line 5
            echo "  ";
        } else {
            // line 6
            echo "    ";
            echo _gettext("Databases");
            // line 7
            echo "  ";
        }
        // line 8
        echo "</h2>

";
        // line 10
        if (($context["is_create_database_shown"] ?? null)) {
            // line 11
            echo "  <ul>
    <li id=\"li_create_database\" class=\"no_bullets\">
      ";
            // line 13
            if (($context["has_create_database_privileges"] ?? null)) {
                // line 14
                echo "        <form method=\"post\" action=\"server_databases.php\" id=\"create_database_form\" class=\"ajax\">
          <p>
            <strong>
              <label for=\"text_create_db\">
                ";
                // line 18
                echo PhpMyAdmin\Util::getImage("b_newdb");
                echo "
                ";
                // line 19
                echo _gettext("Create database");
                // line 20
                echo "              </label>
              ";
                // line 21
                echo PhpMyAdmin\Util::showMySQLDocu("CREATE_DATABASE");
                echo "
            </strong>
          </p>

          ";
                // line 25
                echo PhpMyAdmin\Url::getHiddenInputs("", "", 5);
                echo "
          <input type=\"hidden\" name=\"reload\" value=\"1\">
          ";
                // line 27
                if (($context["has_statistics"] ?? null)) {
                    // line 28
                    echo "            <input type=\"hidden\" name=\"statistics\" value=\"1\">
          ";
                }
                // line 30
                echo "
          <input type=\"text\" name=\"new_db\" maxlength=\"64\" class=\"textfield\" value=\"";
                // line 32
                echo twig_escape_filter($this->env, ($context["database_to_create"] ?? null), "html", null, true);
                echo "\" id=\"text_create_db\" placeholder=\"";
                // line 33
                echo _gettext("Database name");
                echo "\" required>
          ";
                // line 34
                echo ($context["collation_dropdown_box"] ?? null);
                echo "
          <input id=\"buttonGo\" class=\"btn btn-primary\" type=\"submit\" value=\"";
                // line 35
                echo _gettext("Create");
                echo "\">
        </form>
      ";
            } else {
                // line 38
                echo "        <p>
          <strong>
            ";
                // line 40
                echo PhpMyAdmin\Util::getImage("b_newdb");
                echo "
            ";
                // line 41
                echo _gettext("Create database");
                // line 42
                echo "            ";
                echo PhpMyAdmin\Util::showMySQLDocu("CREATE_DATABASE");
                echo "
          </strong>
        </p>

        <span class=\"noPrivileges\">
          ";
                // line 47
                echo PhpMyAdmin\Util::getImage("s_error", "", ["hspace" => 2, "border" => 0, "align" => "middle"]);
                // line 51
                echo "
          ";
                // line 52
                echo _gettext("No privileges to create databases");
                // line 53
                echo "        </span>
      ";
            }
            // line 55
            echo "    </li>
  </ul>
";
        }
        // line 58
        echo "
";
        // line 59
        if ((($context["database_count"] ?? null) > 0)) {
            // line 60
            echo "  ";
            $this->loadTemplate("filter.twig", "server/databases/index.twig", 60)->display(twig_to_array(["filter_value" => ""]));
            // line 61
            echo "
  <div id=\"tableslistcontainer\">
    ";
            // line 63
            echo PhpMyAdmin\Util::getListNavigator(            // line 64
($context["database_count"] ?? null),             // line 65
($context["pos"] ?? null),             // line 66
($context["url_params"] ?? null), "server_databases.php", "frame_content",             // line 69
($context["max_db_list"] ?? null));
            // line 70
            echo "

    <form class=\"ajax\" action=\"server_databases.php\" method=\"post\" name=\"dbStatsForm\" id=\"dbStatsForm\">
      ";
            // line 73
            echo PhpMyAdmin\Url::getHiddenInputs(($context["url_params"] ?? null));
            echo "
      <div class=\"responsivetable\">
        <table id=\"tabledatabases\" class=\"data\">
          <thead>
            <tr>
              ";
            // line 78
            if (($context["is_drop_allowed"] ?? null)) {
                // line 79
                echo "                <th></th>
              ";
            }
            // line 81
            echo "              <th>
                <a href=\"server_databases.php";
            // line 82
            echo PhpMyAdmin\Url::getCommon(twig_array_merge(($context["url_params"] ?? null), ["sort_by" => "SCHEMA_NAME", "sort_order" => ((((twig_get_attribute($this->env, $this->source,             // line 84
($context["url_params"] ?? null), "sort_by", [], "any", false, false, false, 84) == "SCHEMA_NAME") && (twig_get_attribute($this->env, $this->source,             // line 85
($context["url_params"] ?? null), "sort_order", [], "any", false, false, false, 85) == "asc"))) ? ("desc") : ("asc"))]));
            // line 86
            echo "\">
                  ";
            // line 87
            echo _gettext("Database");
            // line 88
            echo "                  ";
            if ((twig_get_attribute($this->env, $this->source, ($context["url_params"] ?? null), "sort_by", [], "any", false, false, false, 88) == "SCHEMA_NAME")) {
                // line 89
                echo "                    ";
                if ((twig_get_attribute($this->env, $this->source, ($context["url_params"] ?? null), "sort_order", [], "any", false, false, false, 89) == "asc")) {
                    // line 90
                    echo "                      ";
                    echo PhpMyAdmin\Util::getImage("s_asc", _gettext("Ascending"));
                    echo "
                    ";
                } else {
                    // line 92
                    echo "                      ";
                    echo PhpMyAdmin\Util::getImage("s_desc", _gettext("Descending"));
                    echo "
                    ";
                }
                // line 94
                echo "                  ";
            }
            // line 95
            echo "                </a>
              </th>

              <th>
                <a href=\"server_databases.php";
            // line 99
            echo PhpMyAdmin\Url::getCommon(twig_array_merge(($context["url_params"] ?? null), ["sort_by" => "DEFAULT_COLLATION_NAME", "sort_order" => ((((twig_get_attribute($this->env, $this->source,             // line 101
($context["url_params"] ?? null), "sort_by", [], "any", false, false, false, 101) == "DEFAULT_COLLATION_NAME") && (twig_get_attribute($this->env, $this->source,             // line 102
($context["url_params"] ?? null), "sort_order", [], "any", false, false, false, 102) == "asc"))) ? ("desc") : ("asc"))]));
            // line 103
            echo "\">
                  ";
            // line 104
            echo _gettext("Collation");
            // line 105
            echo "                  ";
            if ((twig_get_attribute($this->env, $this->source, ($context["url_params"] ?? null), "sort_by", [], "any", false, false, false, 105) == "DEFAULT_COLLATION_NAME")) {
                // line 106
                echo "                    ";
                if ((twig_get_attribute($this->env, $this->source, ($context["url_params"] ?? null), "sort_order", [], "any", false, false, false, 106) == "asc")) {
                    // line 107
                    echo "                      ";
                    echo PhpMyAdmin\Util::getImage("s_asc", _gettext("Ascending"));
                    echo "
                    ";
                } else {
                    // line 109
                    echo "                      ";
                    echo PhpMyAdmin\Util::getImage("s_desc", _gettext("Descending"));
                    echo "
                    ";
                }
                // line 111
                echo "                  ";
            }
            // line 112
            echo "                </a>
              </th>

              ";
            // line 115
            if (($context["has_statistics"] ?? null)) {
                // line 116
                echo "                ";
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable(($context["header_statistics"] ?? null));
                foreach ($context['_seq'] as $context["name"] => $context["statistic"]) {
                    // line 117
                    echo "                  <th";
                    echo (((twig_get_attribute($this->env, $this->source, $context["statistic"], "format", [], "any", false, false, false, 117) == "byte")) ? (" colspan=\"2\"") : (""));
                    echo ">
                    <a href=\"server_databases.php";
                    // line 118
                    echo PhpMyAdmin\Url::getCommon(twig_array_merge(($context["url_params"] ?? null), ["sort_by" =>                     // line 119
$context["name"], "sort_order" => ((((twig_get_attribute($this->env, $this->source,                     // line 120
($context["url_params"] ?? null), "sort_by", [], "any", false, false, false, 120) == $context["name"]) && (twig_get_attribute($this->env, $this->source,                     // line 121
($context["url_params"] ?? null), "sort_order", [], "any", false, false, false, 121) == "asc"))) ? ("desc") : ("asc"))]));
                    // line 122
                    echo "\">
                      ";
                    // line 123
                    echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["statistic"], "title", [], "any", false, false, false, 123), "html", null, true);
                    echo "
                      ";
                    // line 124
                    if ((twig_get_attribute($this->env, $this->source, ($context["url_params"] ?? null), "sort_by", [], "any", false, false, false, 124) == $context["name"])) {
                        // line 125
                        echo "                        ";
                        if ((twig_get_attribute($this->env, $this->source, ($context["url_params"] ?? null), "sort_order", [], "any", false, false, false, 125) == "asc")) {
                            // line 126
                            echo "                          ";
                            echo PhpMyAdmin\Util::getImage("s_asc", _gettext("Ascending"));
                            echo "
                        ";
                        } else {
                            // line 128
                            echo "                          ";
                            echo PhpMyAdmin\Util::getImage("s_desc", _gettext("Descending"));
                            echo "
                        ";
                        }
                        // line 130
                        echo "                      ";
                    }
                    // line 131
                    echo "                    </a>
                  </th>
                ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['name'], $context['statistic'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 134
                echo "              ";
            }
            // line 135
            echo "
              ";
            // line 136
            if (($context["has_master_replication"] ?? null)) {
                // line 137
                echo "                <th>";
                echo _gettext("Master replication");
                echo "</th>
              ";
            }
            // line 139
            echo "
              ";
            // line 140
            if (($context["has_slave_replication"] ?? null)) {
                // line 141
                echo "                <th>";
                echo _gettext("Slave replication");
                echo "</th>
              ";
            }
            // line 143
            echo "
              <th>";
            // line 144
            echo _gettext("Action");
            echo "</th>
            </tr>
          </thead>

          <tbody>
            ";
            // line 149
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["databases"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["database"]) {
                // line 150
                echo "              <tr class=\"db-row";
                echo ((twig_get_attribute($this->env, $this->source, $context["database"], "is_system_schema", [], "any", false, false, false, 150)) ? (" noclick") : (""));
                echo "\" data-filter-row=\"";
                echo twig_escape_filter($this->env, twig_upper_filter($this->env, twig_get_attribute($this->env, $this->source, $context["database"], "name", [], "any", false, false, false, 150)), "html", null, true);
                echo "\">
                ";
                // line 151
                if (($context["is_drop_allowed"] ?? null)) {
                    // line 152
                    echo "                  <td class=\"tool\">
                    <input type=\"checkbox\" name=\"selected_dbs[]\" class=\"checkall\" title=\"";
                    // line 154
                    echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["database"], "name", [], "any", false, false, false, 154), "html", null, true);
                    echo "\" value=\"";
                    // line 155
                    echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["database"], "name", [], "any", false, false, false, 155), "html", null, true);
                    echo "\"";
                    // line 156
                    echo ((twig_get_attribute($this->env, $this->source, $context["database"], "is_system_schema", [], "any", false, false, false, 156)) ? (" disabled") : (""));
                    echo ">
                  </td>
                ";
                }
                // line 159
                echo "
                <td class=\"name\">
                  <a href=\"";
                // line 161
                echo PhpMyAdmin\Util::getScriptNameForOption(($context["default_tab_database"] ?? null), "database");
                // line 162
                echo PhpMyAdmin\Url::getCommon(["db" => twig_get_attribute($this->env, $this->source, $context["database"], "name", [], "any", false, false, false, 162)]);
                echo "\" title=\"";
                // line 163
                echo twig_escape_filter($this->env, sprintf(_gettext("Jump to database '%s'"), twig_get_attribute($this->env, $this->source, $context["database"], "name", [], "any", false, false, false, 163)), "html", null, true);
                echo "\">
                    ";
                // line 164
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["database"], "name", [], "any", false, false, false, 164), "html", null, true);
                echo "
                  </a>
                </td>

                <td class=\"value\">
                  <dfn title=\"";
                // line 169
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["database"], "collation", [], "any", false, false, false, 169), "description", [], "any", false, false, false, 169), "html", null, true);
                echo "\">
                    ";
                // line 170
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["database"], "collation", [], "any", false, false, false, 170), "name", [], "any", false, false, false, 170), "html", null, true);
                echo "
                  </dfn>
                </td>

                ";
                // line 174
                if (($context["has_statistics"] ?? null)) {
                    // line 175
                    echo "                  ";
                    $context['_parent'] = $context;
                    $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, $context["database"], "statistics", [], "any", false, false, false, 175));
                    foreach ($context['_seq'] as $context["_key"] => $context["statistic"]) {
                        // line 176
                        echo "                    ";
                        if ((twig_get_attribute($this->env, $this->source, $context["statistic"], "format", [], "any", false, false, false, 176) === "byte")) {
                            // line 177
                            echo "                      ";
                            $context["value"] = PhpMyAdmin\Util::formatByteDown(twig_get_attribute($this->env, $this->source, $context["statistic"], "raw", [], "any", false, false, false, 177), 3, 1);
                            // line 178
                            echo "                      <td class=\"value\">
                        <data value=\"";
                            // line 179
                            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["statistic"], "raw", [], "any", false, false, false, 179), "html", null, true);
                            echo "\" title=\"";
                            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["statistic"], "raw", [], "any", false, false, false, 179), "html", null, true);
                            echo "\">
                          ";
                            // line 180
                            echo twig_escape_filter($this->env, (($__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4 = ($context["value"] ?? null)) && is_array($__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4) || $__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4 instanceof ArrayAccess ? ($__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4[0] ?? null) : null), "html", null, true);
                            echo "
                        </data>
                      </td>
                      <td class=\"unit\">";
                            // line 183
                            echo twig_escape_filter($this->env, (($__internal_62824350bc4502ee19dbc2e99fc6bdd3bd90e7d8dd6e72f42c35efd048542144 = ($context["value"] ?? null)) && is_array($__internal_62824350bc4502ee19dbc2e99fc6bdd3bd90e7d8dd6e72f42c35efd048542144) || $__internal_62824350bc4502ee19dbc2e99fc6bdd3bd90e7d8dd6e72f42c35efd048542144 instanceof ArrayAccess ? ($__internal_62824350bc4502ee19dbc2e99fc6bdd3bd90e7d8dd6e72f42c35efd048542144[1] ?? null) : null), "html", null, true);
                            echo "</td>
                    ";
                        } else {
                            // line 185
                            echo "                      <td class=\"value\">
                        <data value=\"";
                            // line 186
                            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["statistic"], "raw", [], "any", false, false, false, 186), "html", null, true);
                            echo "\" title=\"";
                            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["statistic"], "raw", [], "any", false, false, false, 186), "html", null, true);
                            echo "\">
                          ";
                            // line 187
                            echo twig_escape_filter($this->env, PhpMyAdmin\Util::formatNumber(twig_get_attribute($this->env, $this->source, $context["statistic"], "raw", [], "any", false, false, false, 187), 0), "html", null, true);
                            echo "
                        </data>
                      </td>
                    ";
                        }
                        // line 191
                        echo "                  ";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['statistic'], $context['_parent'], $context['loop']);
                    $context = array_intersect_key($context, $_parent) + $_parent;
                    // line 192
                    echo "                ";
                }
                // line 193
                echo "
                ";
                // line 194
                if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["database"], "replication", [], "any", false, false, false, 194), "master", [], "any", false, false, false, 194), "status", [], "any", false, false, false, 194)) {
                    // line 195
                    echo "                  ";
                    if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["database"], "replication", [], "any", false, false, false, 195), "master", [], "any", false, false, false, 195), "is_replicated", [], "any", false, false, false, 195)) {
                        // line 196
                        echo "                    <td class=\"tool center\">
                      ";
                        // line 197
                        echo PhpMyAdmin\Util::getIcon("s_success", _gettext("Replicated"));
                        echo "
                    </td>
                  ";
                    } else {
                        // line 200
                        echo "                    <td class=\"tool center\">
                      ";
                        // line 201
                        echo PhpMyAdmin\Util::getIcon("s_cancel", _gettext("Not replicated"));
                        echo "
                    </td>
                  ";
                    }
                    // line 204
                    echo "                ";
                }
                // line 205
                echo "
                ";
                // line 206
                if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["database"], "replication", [], "any", false, false, false, 206), "slave", [], "any", false, false, false, 206), "status", [], "any", false, false, false, 206)) {
                    // line 207
                    echo "                  ";
                    if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["database"], "replication", [], "any", false, false, false, 207), "slave", [], "any", false, false, false, 207), "is_replicated", [], "any", false, false, false, 207)) {
                        // line 208
                        echo "                    <td class=\"tool center\">
                      ";
                        // line 209
                        echo PhpMyAdmin\Util::getIcon("s_success", _gettext("Replicated"));
                        echo "
                    </td>
                  ";
                    } else {
                        // line 212
                        echo "                    <td class=\"tool center\">
                      ";
                        // line 213
                        echo PhpMyAdmin\Util::getIcon("s_cancel", _gettext("Not replicated"));
                        echo "
                    </td>
                  ";
                    }
                    // line 216
                    echo "                ";
                }
                // line 217
                echo "
                <td class=\"tool\">
                  <a class=\"server_databases\" data=\"";
                // line 220
                echo PhpMyAdmin\Sanitize::jsFormat(twig_get_attribute($this->env, $this->source, $context["database"], "name", [], "any", false, false, false, 220));
                echo "\" href=\"server_privileges.php";
                // line 221
                echo PhpMyAdmin\Url::getCommon(["db" => twig_get_attribute($this->env, $this->source,                 // line 222
$context["database"], "name", [], "any", false, false, false, 222), "checkprivsdb" => twig_get_attribute($this->env, $this->source,                 // line 223
$context["database"], "name", [], "any", false, false, false, 223)]);
                // line 224
                echo "\" title=\"";
                // line 225
                echo twig_escape_filter($this->env, sprintf(_gettext("Check privileges for database \"%s\"."), twig_get_attribute($this->env, $this->source, $context["database"], "name", [], "any", false, false, false, 225)), "html", null, true);
                echo "\">
                    ";
                // line 226
                echo PhpMyAdmin\Util::getIcon("s_rights", _gettext("Check privileges"));
                echo "
                  </a>
                </td>
              </tr>
            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['database'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 231
            echo "          </tbody>

          <tfoot>
            <tr>
              <th colspan=\"";
            // line 235
            echo ((($context["is_drop_allowed"] ?? null)) ? ("3") : ("2"));
            echo "\">
                ";
            // line 236
            echo _gettext("Total:");
            // line 237
            echo "                <span id=\"filter-rows-count\">";
            // line 238
            echo twig_escape_filter($this->env, ($context["database_count"] ?? null), "html", null, true);
            // line 239
            echo "</span>
              </th>

              ";
            // line 242
            if (($context["has_statistics"] ?? null)) {
                // line 243
                echo "                ";
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable(($context["total_statistics"] ?? null));
                foreach ($context['_seq'] as $context["_key"] => $context["statistic"]) {
                    // line 244
                    echo "                  ";
                    if ((twig_get_attribute($this->env, $this->source, $context["statistic"], "format", [], "any", false, false, false, 244) === "byte")) {
                        // line 245
                        echo "                    ";
                        $context["value"] = PhpMyAdmin\Util::formatByteDown(twig_get_attribute($this->env, $this->source, $context["statistic"], "raw", [], "any", false, false, false, 245), 3, 1);
                        // line 246
                        echo "                    <th class=\"value\">
                      <data value=\"";
                        // line 247
                        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["statistic"], "raw", [], "any", false, false, false, 247), "html", null, true);
                        echo "\" title=\"";
                        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["statistic"], "raw", [], "any", false, false, false, 247), "html", null, true);
                        echo "\">
                        ";
                        // line 248
                        echo twig_escape_filter($this->env, (($__internal_1cfccaec8dd2e8578ccb026fbe7f2e7e29ac2ed5deb976639c5fc99a6ea8583b = ($context["value"] ?? null)) && is_array($__internal_1cfccaec8dd2e8578ccb026fbe7f2e7e29ac2ed5deb976639c5fc99a6ea8583b) || $__internal_1cfccaec8dd2e8578ccb026fbe7f2e7e29ac2ed5deb976639c5fc99a6ea8583b instanceof ArrayAccess ? ($__internal_1cfccaec8dd2e8578ccb026fbe7f2e7e29ac2ed5deb976639c5fc99a6ea8583b[0] ?? null) : null), "html", null, true);
                        echo "
                      </data>
                    </th>
                    <th class=\"unit\">";
                        // line 251
                        echo twig_escape_filter($this->env, (($__internal_68aa442c1d43d3410ea8f958ba9090f3eaa9a76f8de8fc9be4d6c7389ba28002 = ($context["value"] ?? null)) && is_array($__internal_68aa442c1d43d3410ea8f958ba9090f3eaa9a76f8de8fc9be4d6c7389ba28002) || $__internal_68aa442c1d43d3410ea8f958ba9090f3eaa9a76f8de8fc9be4d6c7389ba28002 instanceof ArrayAccess ? ($__internal_68aa442c1d43d3410ea8f958ba9090f3eaa9a76f8de8fc9be4d6c7389ba28002[1] ?? null) : null), "html", null, true);
                        echo "</th>
                  ";
                    } else {
                        // line 253
                        echo "                    <th class=\"value\">
                      <data value=\"";
                        // line 254
                        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["statistic"], "raw", [], "any", false, false, false, 254), "html", null, true);
                        echo "\" title=\"";
                        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["statistic"], "raw", [], "any", false, false, false, 254), "html", null, true);
                        echo "\">
                        ";
                        // line 255
                        echo twig_escape_filter($this->env, PhpMyAdmin\Util::formatNumber(twig_get_attribute($this->env, $this->source, $context["statistic"], "raw", [], "any", false, false, false, 255), 0), "html", null, true);
                        echo "
                      </data>
                    </th>
                  ";
                    }
                    // line 259
                    echo "                ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['statistic'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 260
                echo "              ";
            }
            // line 261
            echo "
              ";
            // line 262
            if (($context["has_master_replication"] ?? null)) {
                // line 263
                echo "                <th></th>
              ";
            }
            // line 265
            echo "
              ";
            // line 266
            if (($context["has_slave_replication"] ?? null)) {
                // line 267
                echo "                <th></th>
              ";
            }
            // line 269
            echo "
              <th></th>
            </tr>
          </tfoot>
        </table>
      </div>

      ";
            // line 277
            echo "      ";
            if (($context["is_drop_allowed"] ?? null)) {
                // line 278
                echo "        ";
                $this->loadTemplate("select_all.twig", "server/databases/index.twig", 278)->display(twig_to_array(["pma_theme_image" =>                 // line 279
($context["pma_theme_image"] ?? null), "text_dir" =>                 // line 280
($context["text_dir"] ?? null), "form_name" => "dbStatsForm"]));
                // line 283
                echo "
        ";
                // line 284
                echo PhpMyAdmin\Util::getButtonOrImage("", "mult_submit ajax", _gettext("Drop"), "b_deltbl");
                // line 289
                echo "
      ";
            }
            // line 291
            echo "
      ";
            // line 293
            echo "      ";
            if ( !($context["has_statistics"] ?? null)) {
                // line 294
                echo "        ";
                echo call_user_func_array($this->env->getFilter('notice')->getCallable(), [_gettext("Note: Enabling the database statistics here might cause heavy traffic between the web server and the MySQL server.")]);
                echo "

        ";
                // line 296
                ob_start();
                // line 297
                echo "          <strong>";
                echo _gettext("Enable statistics");
                echo "</strong>
        ";
                $context["content"] = ('' === $tmp = ob_get_clean()) ? '' : new Markup($tmp, $this->env->getCharset());
                // line 299
                echo "        ";
                $context["items"] = [0 => ["content" =>                 // line 300
($context["content"] ?? null), "class" => "li_switch_dbstats", "url" => ["href" => ("server_databases.php" . PhpMyAdmin\Url::getCommon(["statistics" => "1"])), "title" => _gettext("Enable statistics")]]];
                // line 307
                echo "        ";
                $this->loadTemplate("list/unordered.twig", "server/databases/index.twig", 307)->display(twig_to_array(["items" => ($context["items"] ?? null)]));
                // line 308
                echo "      ";
            }
            // line 309
            echo "    </form>
  </div>
";
        } else {
            // line 312
            echo "  <p>";
            echo call_user_func_array($this->env->getFilter('notice')->getCallable(), [_gettext("No databases")]);
            echo "</p>
";
        }
    }

    public function getTemplateName()
    {
        return "server/databases/index.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  717 => 312,  712 => 309,  709 => 308,  706 => 307,  704 => 300,  702 => 299,  696 => 297,  694 => 296,  688 => 294,  685 => 293,  682 => 291,  678 => 289,  676 => 284,  673 => 283,  671 => 280,  670 => 279,  668 => 278,  665 => 277,  656 => 269,  652 => 267,  650 => 266,  647 => 265,  643 => 263,  641 => 262,  638 => 261,  635 => 260,  629 => 259,  622 => 255,  616 => 254,  613 => 253,  608 => 251,  602 => 248,  596 => 247,  593 => 246,  590 => 245,  587 => 244,  582 => 243,  580 => 242,  575 => 239,  573 => 238,  571 => 237,  569 => 236,  565 => 235,  559 => 231,  548 => 226,  544 => 225,  542 => 224,  540 => 223,  539 => 222,  538 => 221,  535 => 220,  531 => 217,  528 => 216,  522 => 213,  519 => 212,  513 => 209,  510 => 208,  507 => 207,  505 => 206,  502 => 205,  499 => 204,  493 => 201,  490 => 200,  484 => 197,  481 => 196,  478 => 195,  476 => 194,  473 => 193,  470 => 192,  464 => 191,  457 => 187,  451 => 186,  448 => 185,  443 => 183,  437 => 180,  431 => 179,  428 => 178,  425 => 177,  422 => 176,  417 => 175,  415 => 174,  408 => 170,  404 => 169,  396 => 164,  392 => 163,  389 => 162,  387 => 161,  383 => 159,  377 => 156,  374 => 155,  371 => 154,  368 => 152,  366 => 151,  359 => 150,  355 => 149,  347 => 144,  344 => 143,  338 => 141,  336 => 140,  333 => 139,  327 => 137,  325 => 136,  322 => 135,  319 => 134,  311 => 131,  308 => 130,  302 => 128,  296 => 126,  293 => 125,  291 => 124,  287 => 123,  284 => 122,  282 => 121,  281 => 120,  280 => 119,  279 => 118,  274 => 117,  269 => 116,  267 => 115,  262 => 112,  259 => 111,  253 => 109,  247 => 107,  244 => 106,  241 => 105,  239 => 104,  236 => 103,  234 => 102,  233 => 101,  232 => 99,  226 => 95,  223 => 94,  217 => 92,  211 => 90,  208 => 89,  205 => 88,  203 => 87,  200 => 86,  198 => 85,  197 => 84,  196 => 82,  193 => 81,  189 => 79,  187 => 78,  179 => 73,  174 => 70,  172 => 69,  171 => 66,  170 => 65,  169 => 64,  168 => 63,  164 => 61,  161 => 60,  159 => 59,  156 => 58,  151 => 55,  147 => 53,  145 => 52,  142 => 51,  140 => 47,  131 => 42,  129 => 41,  125 => 40,  121 => 38,  115 => 35,  111 => 34,  107 => 33,  104 => 32,  101 => 30,  97 => 28,  95 => 27,  90 => 25,  83 => 21,  80 => 20,  78 => 19,  74 => 18,  68 => 14,  66 => 13,  62 => 11,  60 => 10,  56 => 8,  53 => 7,  50 => 6,  47 => 5,  44 => 4,  42 => 3,  38 => 2,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "server/databases/index.twig", "/var/www/html/secure/phpMyAdmin/templates/server/databases/index.twig");
    }
}

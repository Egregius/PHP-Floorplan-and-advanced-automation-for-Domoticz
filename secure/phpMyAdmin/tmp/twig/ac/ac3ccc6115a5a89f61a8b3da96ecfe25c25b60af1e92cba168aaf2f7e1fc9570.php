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

/* table/index_form.twig */
class __TwigTemplate_fdc3548b6e0aa78d9580c722d354232897218bea6ae29f701a1444bd8be39544 extends \Twig\Template
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
        echo "<form action=\"tbl_indexes.php\"
    method=\"post\"
    name=\"index_frm\"
    id=\"index_frm\"
    class=\"ajax\">

    ";
        // line 7
        echo PhpMyAdmin\Url::getHiddenInputs(($context["form_params"] ?? null));
        echo "

    <fieldset id=\"index_edit_fields\">
        <div class=\"index_info\">
            <div>
                <div class=\"label\">
                    <strong>
                        <label for=\"input_index_name\">
                            ";
        // line 15
        echo _gettext("Index name:");
        // line 16
        echo "                            ";
        echo PhpMyAdmin\Util::showHint(_gettext("\"PRIMARY\" <b>must</b> be the name of and <b>only of</b> a primary key!"));
        echo "
                        </label>
                    </strong>
                </div>

                <input type=\"text\"
                    name=\"index[Key_name]\"
                    id=\"input_index_name\"
                    size=\"25\"
                    maxlength=\"64\"
                    value=\"";
        // line 26
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["index"] ?? null), "getName", [], "method", false, false, false, 26), "html", null, true);
        echo "\"
                    onfocus=\"this.select()\">
            </div>

            <div>
                <div class=\"label\">
                    <strong>
                        <label for=\"select_index_choice\">
                            ";
        // line 34
        echo _gettext("Index choice:");
        // line 35
        echo "                            ";
        echo PhpMyAdmin\Util::showMySQLDocu("ALTER_TABLE");
        echo "
                        </label>
                    </strong>
                </div>
                ";
        // line 39
        echo twig_get_attribute($this->env, $this->source, ($context["index"] ?? null), "generateIndexChoiceSelector", [0 => ($context["create_edit_table"] ?? null)], "method", false, false, false, 39);
        echo "
            </div>

            ";
        // line 42
        $this->loadTemplate("div_for_slider_effect.twig", "table/index_form.twig", 42)->display(twig_to_array(["id" => "indexoptions", "message" => _gettext("Advanced Options"), "initial_sliders_state" =>         // line 45
($context["default_sliders_state"] ?? null)]));
        // line 47
        echo "
            <div>
                <div class=\"label\">
                    <strong>
                        <label for=\"input_key_block_size\">
                            ";
        // line 52
        echo _gettext("Key block size:");
        // line 53
        echo "                        </label>
                    </strong>
                </div>

                <input type=\"text\"
                    name=\"index[Key_block_size]\"
                    id=\"input_key_block_size\"
                    size=\"30\"
                    value=\"";
        // line 61
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["index"] ?? null), "getKeyBlockSize", [], "method", false, false, false, 61), "html", null, true);
        echo "\">
            </div>

            <div>

                <div class=\"label\">
                    <strong>
                        <label for=\"select_index_type\">
                            ";
        // line 69
        echo _gettext("Index type:");
        // line 70
        echo "                            ";
        echo PhpMyAdmin\Util::showMySQLDocu("ALTER_TABLE");
        echo "
                        </label>
                    </strong>
                </div>
                ";
        // line 74
        echo twig_get_attribute($this->env, $this->source, ($context["index"] ?? null), "generateIndexTypeSelector", [], "method", false, false, false, 74);
        echo "
            </div>

            <div>
                <div class=\"label\">
                    <strong>
                        <label for=\"input_parser\">
                            ";
        // line 81
        echo _gettext("Parser:");
        // line 82
        echo "                        </label>
                    </strong>
                </div>

                <input type=\"text\"
                    name=\"index[Parser]\"
                    id=\"input_parse\"
                    size=\"30\"
                    value=\"";
        // line 90
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["index"] ?? null), "getParser", [], "method", false, false, false, 90), "html", null, true);
        echo "\">
            </div>

            <div>
                <div class=\"label\">
                    <strong>
                        <label for=\"input_index_comment\">
                            ";
        // line 97
        echo _gettext("Comment:");
        // line 98
        echo "                        </label>
                    </strong>
                </div>

                <input type=\"text\"
                    name=\"index[Index_comment]\"
                    id=\"input_index_comment\"
                    size=\"30\"
                    maxlength=\"1024\"
                    value=\"";
        // line 107
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["index"] ?? null), "getComment", [], "method", false, false, false, 107), "html", null, true);
        echo "\">
            </div>
        </div>
        <!-- end of indexoptions div -->

        <div class=\"clearfloat\"></div>

        <table id=\"index_columns\">
            <thead>
                <tr>
                    <th></th>
                    <th>
                        ";
        // line 119
        echo _gettext("Column");
        // line 120
        echo "                    </th>
                    <th>
                        ";
        // line 122
        echo _gettext("Size");
        // line 123
        echo "                    </th>
                </tr>
            </thead>
            ";
        // line 126
        $context["spatial_types"] = [0 => "geometry", 1 => "point", 2 => "linestring", 3 => "polygon", 4 => "multipoint", 5 => "multilinestring", 6 => "multipolygon", 7 => "geomtrycollection"];
        // line 136
        echo "            <tbody>
                ";
        // line 137
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["index"] ?? null), "getColumns", [], "method", false, false, false, 137));
        foreach ($context['_seq'] as $context["_key"] => $context["column"]) {
            // line 138
            echo "                    <tr class=\"noclick\">
                        <td>
                            <span class=\"drag_icon\" title=\"";
            // line 140
            echo _gettext("Drag to reorder");
            echo "\"></span>
                        </td>
                        <td>
                            <select name=\"index[columns][names][]\">
                                <option value=\"\">
                                    -- ";
            // line 145
            echo _gettext("Ignore");
            echo " --
                                </option>
                                ";
            // line 147
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["fields"] ?? null));
            foreach ($context['_seq'] as $context["field_name"] => $context["field_type"]) {
                // line 148
                echo "                                    ";
                if ((((twig_get_attribute($this->env, $this->source, ($context["index"] ?? null), "getChoice", [], "method", false, false, false, 148) != "FULLTEXT") || preg_match("/(char|text)/i",                 // line 149
$context["field_type"])) && ((twig_get_attribute($this->env, $this->source,                 // line 150
($context["index"] ?? null), "getChoice", [], "method", false, false, false, 150) != "SPATIAL") || twig_in_filter(                // line 151
$context["field_type"], ($context["spatial_types"] ?? null))))) {
                    // line 152
                    echo "
                                        <option value=\"";
                    // line 153
                    echo twig_escape_filter($this->env, $context["field_name"], "html", null, true);
                    echo "\"";
                    // line 154
                    if (($context["field_name"] == twig_get_attribute($this->env, $this->source, $context["column"], "getName", [], "method", false, false, false, 154))) {
                        // line 155
                        echo "                                                selected=\"selected\"";
                    }
                    // line 156
                    echo ">
                                            ";
                    // line 157
                    echo twig_escape_filter($this->env, $context["field_name"], "html", null, true);
                    echo " [";
                    echo twig_escape_filter($this->env, $context["field_type"], "html", null, true);
                    echo "]
                                        </option>
                                    ";
                }
                // line 160
                echo "                                ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['field_name'], $context['field_type'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 161
            echo "                            </select>
                        </td>
                        <td>
                            <input type=\"text\"
                                size=\"5\"
                                onfocus=\"this.select()\"
                                name=\"index[columns][sub_parts][]\"
                                value=\"";
            // line 169
            (((twig_get_attribute($this->env, $this->source,             // line 168
($context["index"] ?? null), "getChoice", [], "method", false, false, false, 168) != "SPATIAL")) ? (print (twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source,             // line 169
$context["column"], "getSubPart", [], "method", false, false, false, 169), "html", null, true))) : (print ("")));
            echo "\">
                        </td>
                    </tr>
                ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['column'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 173
        echo "                ";
        if ((($context["add_fields"] ?? null) > 0)) {
            // line 174
            echo "                    ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(range(1, ($context["add_fields"] ?? null)));
            foreach ($context['_seq'] as $context["_key"] => $context["i"]) {
                // line 175
                echo "                        <tr class=\"noclick\">
                            <td>
                                <span class=\"drag_icon\" title=\"";
                // line 177
                echo _gettext("Drag to reorder");
                echo "\"></span>
                            </td>
                            <td>
                                <select name=\"index[columns][names][]\">
                                    <option value=\"\">-- ";
                // line 181
                echo _gettext("Ignore");
                echo " --</option>
                                    ";
                // line 182
                $context["j"] = 0;
                // line 183
                echo "                                    ";
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable(($context["fields"] ?? null));
                foreach ($context['_seq'] as $context["field_name"] => $context["field_type"]) {
                    // line 184
                    echo "                                        ";
                    if (($context["create_edit_table"] ?? null)) {
                        // line 185
                        echo "                                            ";
                        $context["col_index"] = (($__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4 = $context["field_type"]) && is_array($__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4) || $__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4 instanceof ArrayAccess ? ($__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4[1] ?? null) : null);
                        // line 186
                        echo "                                            ";
                        $context["field_type"] = (($__internal_62824350bc4502ee19dbc2e99fc6bdd3bd90e7d8dd6e72f42c35efd048542144 = $context["field_type"]) && is_array($__internal_62824350bc4502ee19dbc2e99fc6bdd3bd90e7d8dd6e72f42c35efd048542144) || $__internal_62824350bc4502ee19dbc2e99fc6bdd3bd90e7d8dd6e72f42c35efd048542144 instanceof ArrayAccess ? ($__internal_62824350bc4502ee19dbc2e99fc6bdd3bd90e7d8dd6e72f42c35efd048542144[0] ?? null) : null);
                        // line 187
                        echo "                                        ";
                    }
                    // line 188
                    echo "                                        ";
                    $context["j"] = (($context["j"] ?? null) + 1);
                    // line 189
                    echo "                                        <option value=\"";
                    echo twig_escape_filter($this->env, (((isset($context["col_index"]) || array_key_exists("col_index", $context))) ? (                    // line 190
($context["col_index"] ?? null)) : ($context["field_name"])), "html", null, true);
                    echo "\"";
                    // line 191
                    echo (((($context["j"] ?? null) == $context["i"])) ? (" selected=\"selected\"") : (""));
                    echo ">
                                            ";
                    // line 192
                    echo twig_escape_filter($this->env, $context["field_name"], "html", null, true);
                    echo " [";
                    echo twig_escape_filter($this->env, $context["field_type"], "html", null, true);
                    echo "]
                                        </option>
                                    ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['field_name'], $context['field_type'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 195
                echo "                                </select>
                            </td>
                            <td>
                                <input type=\"text\"
                                    size=\"5\"
                                    onfocus=\"this.select()\"
                                    name=\"index[columns][sub_parts][]\"
                                    value=\"\">
                            </td>
                        </tr>
                    ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['i'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 206
            echo "                ";
        }
        // line 207
        echo "            </tbody>
        </table>
        <div class=\"add_more\">

            <div class=\"slider\"></div>
            <div class=\"add_fields hide\">
                <input class=\"btn btn-secondary\" type=\"submit\"
                    id=\"add_fields\"
                    value=\"";
        // line 215
        echo twig_escape_filter($this->env, sprintf(_gettext("Add %s column(s) to index"), 1), "html", null, true);
        echo "\">
            </div>
        </div>
    </fieldset>
    <fieldset class=\"tblFooters\">
        <button class=\"btn btn-secondary\" type=\"submit\" id=\"preview_index_frm\">";
        // line 220
        echo _gettext("Preview SQL");
        echo "</button>
        <input class=\"btn btn-primary\" type=\"submit\" id=\"save_index_frm\" value=\"";
        // line 221
        echo _gettext("Go");
        echo "\">
    </fieldset>
</form>
";
    }

    public function getTemplateName()
    {
        return "table/index_form.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  404 => 221,  400 => 220,  392 => 215,  382 => 207,  379 => 206,  363 => 195,  352 => 192,  348 => 191,  345 => 190,  343 => 189,  340 => 188,  337 => 187,  334 => 186,  331 => 185,  328 => 184,  323 => 183,  321 => 182,  317 => 181,  310 => 177,  306 => 175,  301 => 174,  298 => 173,  288 => 169,  287 => 168,  286 => 169,  277 => 161,  271 => 160,  263 => 157,  260 => 156,  257 => 155,  255 => 154,  252 => 153,  249 => 152,  247 => 151,  246 => 150,  245 => 149,  243 => 148,  239 => 147,  234 => 145,  226 => 140,  222 => 138,  218 => 137,  215 => 136,  213 => 126,  208 => 123,  206 => 122,  202 => 120,  200 => 119,  185 => 107,  174 => 98,  172 => 97,  162 => 90,  152 => 82,  150 => 81,  140 => 74,  132 => 70,  130 => 69,  119 => 61,  109 => 53,  107 => 52,  100 => 47,  98 => 45,  97 => 42,  91 => 39,  83 => 35,  81 => 34,  70 => 26,  56 => 16,  54 => 15,  43 => 7,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "table/index_form.twig", "/var/www/html/secure/phpMyAdmin/templates/table/index_form.twig");
    }
}

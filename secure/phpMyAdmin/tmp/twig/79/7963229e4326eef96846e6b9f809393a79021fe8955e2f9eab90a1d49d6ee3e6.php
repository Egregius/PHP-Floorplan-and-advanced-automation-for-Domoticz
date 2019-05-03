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

/* database/central_columns/main.twig */
class __TwigTemplate_847d83f8e55fce05dbd3ce25acdd98bcd894ff8c4abf45ddbdf056efed113660 extends \Twig\Template
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
        // line 2
        echo "<div id=\"add_col_div\" class=\"topmargin\">
    <a href=\"#\">
        <span>";
        // line 4
        echo (((($context["total_rows"] ?? null) > 0)) ? ("+") : ("-"));
        echo "</span>";
        echo _gettext("Add new column");
        // line 5
        echo "    </a>
    <form id=\"add_new\" class=\"new_central_col";
        // line 6
        echo (((($context["total_rows"] ?? null) != 0)) ? (" hide") : (""));
        echo "\"
        method=\"post\" action=\"db_central_columns.php\">
        ";
        // line 8
        echo PhpMyAdmin\Url::getHiddenInputs(($context["db"] ?? null));
        echo "
        <input type=\"hidden\" name=\"add_new_column\" value=\"add_new_column\">
        <div class=\"responsivetable\">
            <table>
                <thead>
                    <tr>
                        <th class=\"\"></th>
                        <th class=\"hide\"></th>
                        <th class=\"\" title=\"\" data-column=\"name\">
                            ";
        // line 17
        echo _gettext("Name");
        // line 18
        echo "                            <div class=\"sorticon\"></div>
                        </th>
                        <th class=\"\" title=\"\" data-column=\"type\">
                            ";
        // line 21
        echo _gettext("Type");
        // line 22
        echo "                            <div class=\"sorticon\"></div>
                        </th>
                        <th class=\"\" title=\"\" data-column=\"length\">
                            ";
        // line 25
        echo _gettext("Length/Value");
        // line 26
        echo "                            <div class=\"sorticon\"></div>
                        </th>
                        <th class=\"\" title=\"\" data-column=\"default\">
                            ";
        // line 29
        echo _gettext("Default");
        // line 30
        echo "                            <div class=\"sorticon\"></div>
                        </th>
                        <th class=\"\" title=\"\" data-column=\"collation\">
                            ";
        // line 33
        echo _gettext("Collation");
        // line 34
        echo "                            <div class=\"sorticon\"></div>
                        </th>
                        <th class=\"\" title=\"\" data-column=\"attribute\">
                            ";
        // line 37
        echo _gettext("Attribute");
        // line 38
        echo "                            <div class=\"sorticon\"></div>
                        </th>
                        <th class=\"\" title=\"\" data-column=\"isnull\">
                            ";
        // line 41
        echo _gettext("Null");
        // line 42
        echo "                            <div class=\"sorticon\"></div>
                        </th>
                        <th class=\"\" title=\"\" data-column=\"extra\">
                            ";
        // line 45
        echo _gettext("A_I");
        // line 46
        echo "                            <div class=\"sorticon\"></div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td></td>
                        <td name=\"col_name\" class=\"nowrap\">
                            ";
        // line 54
        $this->loadTemplate("columns_definitions/column_name.twig", "database/central_columns/main.twig", 54)->display(twig_to_array(["column_number" => 0, "ci" => 0, "ci_offset" => 0, "column_meta" => [], "cfg_relation" => ["centralcolumnswork" => false], "max_rows" =>         // line 62
($context["max_rows"] ?? null)]));
        // line 64
        echo "                        </td>
                        <td name=\"col_type\" class=\"nowrap\">
                            ";
        // line 66
        $this->loadTemplate("columns_definitions/column_type.twig", "database/central_columns/main.twig", 66)->display(twig_to_array(["column_number" => 0, "ci" => 1, "ci_offset" => 0, "type_upper" => "", "column_meta" => []]));
        // line 73
        echo "                        </td>
                        <td class=\"nowrap\" name=\"col_length\">
                            ";
        // line 75
        $this->loadTemplate("columns_definitions/column_length.twig", "database/central_columns/main.twig", 75)->display(twig_to_array(["column_number" => 0, "ci" => 2, "ci_offset" => 0, "length_values_input_size" => 8, "length_to_display" => ""]));
        // line 82
        echo "                        </td>
                        <td class=\"nowrap\" name=\"col_default\">
                            ";
        // line 84
        $this->loadTemplate("columns_definitions/column_default.twig", "database/central_columns/main.twig", 84)->display(twig_to_array(["column_number" => 0, "ci" => 3, "ci_offset" => 0, "type_upper" => "", "column_meta" => [], "char_editing" =>         // line 90
($context["char_editing"] ?? null)]));
        // line 92
        echo "                        </td>
                        <td name=\"collation\" class=\"nowrap\">
                            ";
        // line 94
        echo PhpMyAdmin\Charsets::getCollationDropdownBox(        // line 95
($context["dbi"] ?? null),         // line 96
($context["disableIs"] ?? null), "field_collation[0]", "field_0_4", null, false);
        // line 101
        echo "
                        </td>
                        <td class=\"nowrap\" name=\"col_attribute\">
                            ";
        // line 104
        $this->loadTemplate("columns_definitions/column_attribute.twig", "database/central_columns/main.twig", 104)->display(twig_to_array(["column_number" => 0, "ci" => 5, "ci_offset" => 0, "extracted_columnspec" => [], "column_meta" => [], "submit_attribute" => false, "attribute_types" =>         // line 111
($context["attribute_types"] ?? null)]));
        // line 113
        echo "                        </td>
                        <td class=\"nowrap\" name=\"col_isNull\">
                            ";
        // line 115
        $this->loadTemplate("columns_definitions/column_null.twig", "database/central_columns/main.twig", 115)->display(twig_to_array(["column_number" => 0, "ci" => 6, "ci_offset" => 0, "column_meta" => []]));
        // line 121
        echo "                        </td>
                        <td class=\"nowrap\" name=\"col_extra\">
                            ";
        // line 123
        $this->loadTemplate("columns_definitions/column_extra.twig", "database/central_columns/main.twig", 123)->display(twig_to_array(["column_number" => 0, "ci" => 7, "ci_offset" => 0, "column_meta" => []]));
        // line 129
        echo "                        </td>
                        <td>
                            <input id=\"add_column_save\" class=\"btn btn-primary\" type=\"submit\" value=\"Save\">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </form>
</div>
";
        // line 139
        if ((($context["total_rows"] ?? null) <= 0)) {
            // line 140
            echo "    <fieldset>
        ";
            // line 141
            echo _gettext("The central list of columns for the current database is empty");
            // line 142
            echo "    </fieldset>
";
        } else {
            // line 144
            echo "    <table style=\"display:inline-block;max-width:49%\" class=\"navigation nospacing nopadding\">
        <tr>
            <td class=\"navigation_separator\"></td>
            ";
            // line 147
            if (((($context["pos"] ?? null) - ($context["max_rows"] ?? null)) >= 0)) {
                // line 148
                echo "                <td>
                    <form action=\"db_central_columns.php\" method=\"post\">
                        ";
                // line 150
                echo PhpMyAdmin\Url::getHiddenInputs(($context["db"] ?? null));
                echo "
                        <input type=\"hidden\" name=\"pos\" value=\"";
                // line 151
                echo twig_escape_filter($this->env, (($context["pos"] ?? null) - ($context["max_rows"] ?? null)), "html", null, true);
                echo "\">
                        <input type=\"hidden\" name=\"total_rows\" value=\"";
                // line 152
                echo twig_escape_filter($this->env, ($context["total_rows"] ?? null), "html", null, true);
                echo "\">
                        <input class=\"btn btn-secondary ajax\" type=\"submit\" name=\"navig\" value=\"&lt\">
                    </form>
                </td>
            ";
            }
            // line 157
            echo "            ";
            if ((($context["tn_nbTotalPage"] ?? null) > 1)) {
                // line 158
                echo "                <td>
                    <form action=\"db_central_columns.php\" method=\"post\">
                        ";
                // line 160
                echo PhpMyAdmin\Url::getHiddenInputs(($context["db"] ?? null));
                echo "
                        <input type=\"hidden\" name=\"total_rows\" value=\"";
                // line 161
                echo twig_escape_filter($this->env, ($context["total_rows"] ?? null), "html", null, true);
                echo "\">
                        ";
                // line 162
                echo ($context["tn_page_selector"] ?? null);
                echo "
                    </form>
                </td>
            ";
            }
            // line 166
            echo "            ";
            if (((($context["pos"] ?? null) + ($context["max_rows"] ?? null)) < ($context["total_rows"] ?? null))) {
                // line 167
                echo "                <td>
                    <form action=\"db_central_columns.php\" method=\"post\">
                        ";
                // line 169
                echo PhpMyAdmin\Url::getHiddenInputs(($context["db"] ?? null));
                echo "
                        <input type=\"hidden\" name=\"pos\" value=\"";
                // line 170
                echo twig_escape_filter($this->env, (($context["pos"] ?? null) + ($context["max_rows"] ?? null)), "html", null, true);
                echo "\">
                        <input type=\"hidden\" name=\"total_rows\" value=\"";
                // line 171
                echo twig_escape_filter($this->env, ($context["total_rows"] ?? null), "html", null, true);
                echo "\">
                        <input class=\"btn btn-secondary ajax\" type=\"submit\" name=\"navig\" value=\"&gt\">
                    </form>
                </td>
            ";
            }
            // line 176
            echo "            </form>
            </td>
            <td class=\"navigation_separator\"></td>
            <td>
                <span>";
            // line 180
            echo _gettext("Filter rows");
            echo ":</span>
                <input type=\"text\" class=\"filter_rows\" placeholder=\"";
            // line 181
            echo _gettext("Search this table");
            echo "\">
            </td>
            <td class=\"navigation_separator\"></td>
        </tr>
    </table>
";
        }
        // line 188
        echo "<table class=\"central_columns_add_column\" class=\"navigation nospacing nopadding\">
    <tr>
        <td class=\"navigation_separator largescreenonly\"></td>
        <td class=\"central_columns_navigation\">
            ";
        // line 192
        echo PhpMyAdmin\Util::getIcon("centralColumns_add", _gettext("Add column"));
        echo "
            <form id=\"add_column\" action=\"db_central_columns.php\" method=\"post\">
                ";
        // line 194
        echo PhpMyAdmin\Url::getHiddenInputs(($context["db"] ?? null));
        echo "
                <input type=\"hidden\" name=\"add_column\" value=\"add\">
                <input type=\"hidden\" name=\"pos\" value=\"";
        // line 196
        echo twig_escape_filter($this->env, ($context["pos"] ?? null), "html", null, true);
        echo "\">
                <input type=\"hidden\" name=\"total_rows\" value=\"";
        // line 197
        echo twig_escape_filter($this->env, ($context["total_rows"] ?? null), "html", null, true);
        echo "\">
                ";
        // line 199
        echo "                <select name=\"table-select\" id=\"table-select\">
                    <option value=\"\" disabled=\"disabled\" selected=\"selected\">
                        ";
        // line 201
        echo _gettext("Select a table");
        // line 202
        echo "                    </option>
                    ";
        // line 203
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["tables"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["table"]) {
            // line 204
            echo "                        <option value=\"";
            echo twig_escape_filter($this->env, $context["table"]);
            echo "\">";
            echo twig_escape_filter($this->env, $context["table"]);
            echo "</option>
                    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['table'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 206
        echo "                </select>
                <select name=\"column-select\" id=\"column-select\">
                    <option value=\"\" selected=\"selected\">";
        // line 208
        echo _gettext("Select a column.");
        echo "</option>
                </select>
                <input class=\"btn btn-primary\" type=\"submit\" value=\"";
        // line 210
        echo _gettext("Add");
        echo "\">
            </form>
        </td>
        <td class=\"navigation_separator largescreenonly\"></td>
    </tr>
</table>
";
        // line 216
        if ((($context["total_rows"] ?? null) > 0)) {
            // line 217
            echo "    <form method=\"post\" id=\"del_form\" action=\"db_central_columns.php\">
        ";
            // line 218
            echo PhpMyAdmin\Url::getHiddenInputs(($context["db"] ?? null));
            echo "
        <input id=\"del_col_name\" type=\"hidden\" name=\"col_name\" value=\"\">
        <input type=\"hidden\" name=\"pos\" value=\"";
            // line 220
            echo twig_escape_filter($this->env, ($context["pos"] ?? null), "html", null, true);
            echo "\">
        <input type=\"hidden\" name=\"delete_save\" value=\"delete\">
    </form>
    <div id=\"tableslistcontainer\">
        <form name=\"tableslistcontainer\">
            <table id=\"table_columns\" class=\"tablesorter\" class=\"data\">
                ";
            // line 226
            $context["class"] = "column_heading";
            // line 227
            echo "                ";
            $context["title"] = _gettext("Click to sort.");
            // line 228
            echo "                <thead>
                    <tr>
                        <th class=\"";
            // line 230
            echo twig_escape_filter($this->env, ($context["class"] ?? null), "html", null, true);
            echo "\"></th>
                        <th class=\"hide\"></th>
                        <th class=\"column_action\" colspan=\"2\">";
            // line 232
            echo _gettext("Action");
            echo "</th>
                        <th class=\"";
            // line 233
            echo twig_escape_filter($this->env, ($context["class"] ?? null), "html", null, true);
            echo "\" title=\"";
            echo twig_escape_filter($this->env, ($context["title"] ?? null), "html", null, true);
            echo "\" data-column=\"name\">
                            ";
            // line 234
            echo _gettext("Name");
            // line 235
            echo "                            <div class=\"sorticon\"></div>
                        </th>
                        <th class=\"";
            // line 237
            echo twig_escape_filter($this->env, ($context["class"] ?? null), "html", null, true);
            echo "\" title=\"";
            echo twig_escape_filter($this->env, ($context["title"] ?? null), "html", null, true);
            echo "\" data-column=\"type\">
                            ";
            // line 238
            echo _gettext("Type");
            // line 239
            echo "                            <div class=\"sorticon\"></div>
                        </th>
                        <th class=\"";
            // line 241
            echo twig_escape_filter($this->env, ($context["class"] ?? null), "html", null, true);
            echo "\" title=\"";
            echo twig_escape_filter($this->env, ($context["title"] ?? null), "html", null, true);
            echo "\" data-column=\"length\">
                            ";
            // line 242
            echo _gettext("Length/Value");
            // line 243
            echo "                            <div class=\"sorticon\"></div>
                        </th>
                        <th class=\"";
            // line 245
            echo twig_escape_filter($this->env, ($context["class"] ?? null), "html", null, true);
            echo "\" title=\"";
            echo twig_escape_filter($this->env, ($context["title"] ?? null), "html", null, true);
            echo "\" data-column=\"default\">
                            ";
            // line 246
            echo _gettext("Default");
            // line 247
            echo "                            <div class=\"sorticon\"></div>
                        </th>
                        <th class=\"";
            // line 249
            echo twig_escape_filter($this->env, ($context["class"] ?? null), "html", null, true);
            echo "\" title=\"";
            echo twig_escape_filter($this->env, ($context["title"] ?? null), "html", null, true);
            echo "\" data-column=\"collation\">
                            ";
            // line 250
            echo _gettext("Collation");
            // line 251
            echo "                            <div class=\"sorticon\"></div>
                        </th>
                        <th class=\"";
            // line 253
            echo twig_escape_filter($this->env, ($context["class"] ?? null), "html", null, true);
            echo "\" title=\"";
            echo twig_escape_filter($this->env, ($context["title"] ?? null), "html", null, true);
            echo "\" data-column=\"attribute\">
                            ";
            // line 254
            echo _gettext("Attribute");
            // line 255
            echo "                            <div class=\"sorticon\"></div>
                        </th>
                        <th class=\"";
            // line 257
            echo twig_escape_filter($this->env, ($context["class"] ?? null), "html", null, true);
            echo "\" title=\"";
            echo twig_escape_filter($this->env, ($context["title"] ?? null), "html", null, true);
            echo "\" data-column=\"isnull\">
                            ";
            // line 258
            echo _gettext("Null");
            // line 259
            echo "                            <div class=\"sorticon\"></div>
                        </th>
                        <th class=\"";
            // line 261
            echo twig_escape_filter($this->env, ($context["class"] ?? null), "html", null, true);
            echo "\" title=\"";
            echo twig_escape_filter($this->env, ($context["title"] ?? null), "html", null, true);
            echo "\" data-column=\"extra\">
                            ";
            // line 262
            echo _gettext("A_I");
            // line 263
            echo "                            <div class=\"sorticon\"></div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    ";
            // line 268
            $context["row_num"] = 0;
            // line 269
            echo "                    ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["rows_list"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["row"]) {
                // line 270
                echo "                        ";
                // line 271
                echo "                        <tr data-rownum=\"";
                echo twig_escape_filter($this->env, ($context["row_num"] ?? null), "html", null, true);
                echo "\" id=\"";
                echo twig_escape_filter($this->env, ("f_" . ($context["row_num"] ?? null)), "html", null, true);
                echo "\">
                            ";
                // line 272
                echo PhpMyAdmin\Url::getHiddenInputs(($context["db"] ?? null));
                echo "
                            <input type=\"hidden\" name=\"edit_save\" value=\"save\">
                            <td class=\"nowrap\">
                                <input type=\"checkbox\" class=\"checkall\" name=\"selected_fld[]\"
                                value=\"";
                // line 276
                echo twig_escape_filter($this->env, (($__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4 = $context["row"]) && is_array($__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4) || $__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4 instanceof ArrayAccess ? ($__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4["col_name"] ?? null) : null), "html", null, true);
                echo "\" id=\"";
                echo twig_escape_filter($this->env, ("checkbox_row_" . ($context["row_num"] ?? null)), "html", null, true);
                echo "\">
                            </td>
                            <td id=\"";
                // line 278
                echo twig_escape_filter($this->env, ("edit_" . ($context["row_num"] ?? null)), "html", null, true);
                echo "\" class=\"edit center\">
                                <a href=\"#\"> ";
                // line 279
                echo PhpMyAdmin\Util::getIcon("b_edit", _gettext("Edit"));
                echo "</a>
                            </td>
                            <td class=\"del_row\" data-rownum = \"";
                // line 281
                echo twig_escape_filter($this->env, ($context["row_num"] ?? null), "html", null, true);
                echo "\">
                                <a hrf=\"#\">";
                // line 282
                echo PhpMyAdmin\Util::getIcon("b_drop", _gettext("Delete"));
                echo "</a>
                                <input type=\"submit\" data-rownum = \"";
                // line 283
                echo twig_escape_filter($this->env, ($context["row_num"] ?? null), "html", null, true);
                echo "\" class=\"btn btn-secondary edit_cancel_form\" value=\"Cancel\">
                            </td>
                            <td id=\"";
                // line 285
                echo twig_escape_filter($this->env, ("save_" . ($context["row_num"] ?? null)), "html", null, true);
                echo "\" class=\"hide\">
                                <input type=\"submit\" data-rownum=\"";
                // line 286
                echo twig_escape_filter($this->env, ($context["row_num"] ?? null), "html", null, true);
                echo "\" class=\"btn btn-primary edit_save_form\" value=\"Save\">
                            </td>
                            <td name=\"col_name\" class=\"nowrap\">
                                <span>";
                // line 289
                echo twig_escape_filter($this->env, (($__internal_62824350bc4502ee19dbc2e99fc6bdd3bd90e7d8dd6e72f42c35efd048542144 = $context["row"]) && is_array($__internal_62824350bc4502ee19dbc2e99fc6bdd3bd90e7d8dd6e72f42c35efd048542144) || $__internal_62824350bc4502ee19dbc2e99fc6bdd3bd90e7d8dd6e72f42c35efd048542144 instanceof ArrayAccess ? ($__internal_62824350bc4502ee19dbc2e99fc6bdd3bd90e7d8dd6e72f42c35efd048542144["col_name"] ?? null) : null), "html", null, true);
                echo "</span>
                                <input name=\"orig_col_name\" type=\"hidden\" value=\"";
                // line 290
                echo twig_escape_filter($this->env, (($__internal_1cfccaec8dd2e8578ccb026fbe7f2e7e29ac2ed5deb976639c5fc99a6ea8583b = $context["row"]) && is_array($__internal_1cfccaec8dd2e8578ccb026fbe7f2e7e29ac2ed5deb976639c5fc99a6ea8583b) || $__internal_1cfccaec8dd2e8578ccb026fbe7f2e7e29ac2ed5deb976639c5fc99a6ea8583b instanceof ArrayAccess ? ($__internal_1cfccaec8dd2e8578ccb026fbe7f2e7e29ac2ed5deb976639c5fc99a6ea8583b["col_name"] ?? null) : null), "html", null, true);
                echo "\">
                                ";
                // line 291
                $this->loadTemplate("columns_definitions/column_name.twig", "database/central_columns/main.twig", 291)->display(twig_to_array(["column_number" =>                 // line 292
($context["row_num"] ?? null), "ci" => 0, "ci_offset" => 0, "column_meta" => ["Field" => (($__internal_68aa442c1d43d3410ea8f958ba9090f3eaa9a76f8de8fc9be4d6c7389ba28002 =                 // line 296
$context["row"]) && is_array($__internal_68aa442c1d43d3410ea8f958ba9090f3eaa9a76f8de8fc9be4d6c7389ba28002) || $__internal_68aa442c1d43d3410ea8f958ba9090f3eaa9a76f8de8fc9be4d6c7389ba28002 instanceof ArrayAccess ? ($__internal_68aa442c1d43d3410ea8f958ba9090f3eaa9a76f8de8fc9be4d6c7389ba28002["col_name"] ?? null) : null)], "cfg_relation" => ["centralcolumnswork" => false], "max_rows" =>                 // line 301
($context["max_rows"] ?? null)]));
                // line 303
                echo "                            </td>
                            <td name = \"col_type\" class=\"nowrap\">
                                <span>";
                // line 305
                echo twig_escape_filter($this->env, (($__internal_d7fc55f1a54b629533d60b43063289db62e68921ee7a5f8de562bd9d4a2b7ad4 = $context["row"]) && is_array($__internal_d7fc55f1a54b629533d60b43063289db62e68921ee7a5f8de562bd9d4a2b7ad4) || $__internal_d7fc55f1a54b629533d60b43063289db62e68921ee7a5f8de562bd9d4a2b7ad4 instanceof ArrayAccess ? ($__internal_d7fc55f1a54b629533d60b43063289db62e68921ee7a5f8de562bd9d4a2b7ad4["col_type"] ?? null) : null), "html", null, true);
                echo "</span>
                                ";
                // line 306
                $this->loadTemplate("columns_definitions/column_type.twig", "database/central_columns/main.twig", 306)->display(twig_to_array(["column_number" =>                 // line 307
($context["row_num"] ?? null), "ci" => 1, "ci_offset" => 0, "type_upper" => (($__internal_01476f8db28655ee4ee02ea2d17dd5a92599be76304f08cd8bc0e05aced30666 =                 // line 310
($context["types_upper"] ?? null)) && is_array($__internal_01476f8db28655ee4ee02ea2d17dd5a92599be76304f08cd8bc0e05aced30666) || $__internal_01476f8db28655ee4ee02ea2d17dd5a92599be76304f08cd8bc0e05aced30666 instanceof ArrayAccess ? ($__internal_01476f8db28655ee4ee02ea2d17dd5a92599be76304f08cd8bc0e05aced30666[($context["row_num"] ?? null)] ?? null) : null), "column_meta" => []]));
                // line 313
                echo "                            </td>
                            <td class=\"nowrap\" name=\"col_length\">
                                <span>";
                // line 315
                (((($__internal_01c35b74bd85735098add188b3f8372ba465b232ab8298cb582c60f493d3c22e = $context["row"]) && is_array($__internal_01c35b74bd85735098add188b3f8372ba465b232ab8298cb582c60f493d3c22e) || $__internal_01c35b74bd85735098add188b3f8372ba465b232ab8298cb582c60f493d3c22e instanceof ArrayAccess ? ($__internal_01c35b74bd85735098add188b3f8372ba465b232ab8298cb582c60f493d3c22e["col_length"] ?? null) : null)) ? (print (twig_escape_filter($this->env, (($__internal_63ad1f9a2bf4db4af64b010785e9665558fdcac0e8db8b5b413ed986c62dbb52 = $context["row"]) && is_array($__internal_63ad1f9a2bf4db4af64b010785e9665558fdcac0e8db8b5b413ed986c62dbb52) || $__internal_63ad1f9a2bf4db4af64b010785e9665558fdcac0e8db8b5b413ed986c62dbb52 instanceof ArrayAccess ? ($__internal_63ad1f9a2bf4db4af64b010785e9665558fdcac0e8db8b5b413ed986c62dbb52["col_length"] ?? null) : null), "html", null, true))) : (print ("")));
                echo "</span>
                                ";
                // line 316
                $this->loadTemplate("columns_definitions/column_length.twig", "database/central_columns/main.twig", 316)->display(twig_to_array(["column_number" =>                 // line 317
($context["row_num"] ?? null), "ci" => 2, "ci_offset" => 0, "length_values_input_size" => 8, "length_to_display" => (($__internal_f10a4cc339617934220127f034125576ed229e948660ebac906a15846d52f136 =                 // line 321
$context["row"]) && is_array($__internal_f10a4cc339617934220127f034125576ed229e948660ebac906a15846d52f136) || $__internal_f10a4cc339617934220127f034125576ed229e948660ebac906a15846d52f136 instanceof ArrayAccess ? ($__internal_f10a4cc339617934220127f034125576ed229e948660ebac906a15846d52f136["col_length"] ?? null) : null)]));
                // line 323
                echo "                            </td>
                            <td class=\"nowrap\" name=\"col_default\">
                                ";
                // line 325
                if (twig_get_attribute($this->env, $this->source, $context["row"], "col_default", [], "array", true, true, false, 325)) {
                    // line 326
                    echo "                                    <span>";
                    echo twig_escape_filter($this->env, (($__internal_887a873a4dc3cf8bd4f99c487b4c7727999c350cc3a772414714e49a195e4386 = $context["row"]) && is_array($__internal_887a873a4dc3cf8bd4f99c487b4c7727999c350cc3a772414714e49a195e4386) || $__internal_887a873a4dc3cf8bd4f99c487b4c7727999c350cc3a772414714e49a195e4386 instanceof ArrayAccess ? ($__internal_887a873a4dc3cf8bd4f99c487b4c7727999c350cc3a772414714e49a195e4386["col_default"] ?? null) : null), "html", null, true);
                    echo "</span>
                                ";
                } else {
                    // line 328
                    echo "                                    <span>None</span>
                                ";
                }
                // line 330
                echo "                                ";
                $this->loadTemplate("columns_definitions/column_default.twig", "database/central_columns/main.twig", 330)->display(twig_to_array(["column_number" =>                 // line 331
($context["row_num"] ?? null), "ci" => 3, "ci_offset" => 0, "type_upper" => (($__internal_d527c24a729d38501d770b40a0d25e1ce8a7f0bff897cc4f8f449ba71fcff3d9 =                 // line 334
($context["types_upper"] ?? null)) && is_array($__internal_d527c24a729d38501d770b40a0d25e1ce8a7f0bff897cc4f8f449ba71fcff3d9) || $__internal_d527c24a729d38501d770b40a0d25e1ce8a7f0bff897cc4f8f449ba71fcff3d9 instanceof ArrayAccess ? ($__internal_d527c24a729d38501d770b40a0d25e1ce8a7f0bff897cc4f8f449ba71fcff3d9[($context["row_num"] ?? null)] ?? null) : null), "column_meta" => (($__internal_f6dde3a1020453fdf35e718e94f93ce8eb8803b28cc77a665308e14bbe8572ae =                 // line 335
($context["rows_meta"] ?? null)) && is_array($__internal_f6dde3a1020453fdf35e718e94f93ce8eb8803b28cc77a665308e14bbe8572ae) || $__internal_f6dde3a1020453fdf35e718e94f93ce8eb8803b28cc77a665308e14bbe8572ae instanceof ArrayAccess ? ($__internal_f6dde3a1020453fdf35e718e94f93ce8eb8803b28cc77a665308e14bbe8572ae[($context["row_num"] ?? null)] ?? null) : null), "char_editing" =>                 // line 336
($context["char_editing"] ?? null)]));
                // line 338
                echo "                            </td>
                            <td name=\"collation\" class=\"nowrap\">
                                <span>";
                // line 340
                echo twig_escape_filter($this->env, (($__internal_25c0fab8152b8dd6b90603159c0f2e8a936a09ab76edb5e4d7bc95d9a8d2dc8f = $context["row"]) && is_array($__internal_25c0fab8152b8dd6b90603159c0f2e8a936a09ab76edb5e4d7bc95d9a8d2dc8f) || $__internal_25c0fab8152b8dd6b90603159c0f2e8a936a09ab76edb5e4d7bc95d9a8d2dc8f instanceof ArrayAccess ? ($__internal_25c0fab8152b8dd6b90603159c0f2e8a936a09ab76edb5e4d7bc95d9a8d2dc8f["col_collation"] ?? null) : null), "html", null, true);
                echo "</span>
                                ";
                // line 341
                echo PhpMyAdmin\Charsets::getCollationDropdownBox(                // line 342
($context["dbi"] ?? null),                 // line 343
($context["disableIs"] ?? null), (("field_collation[" .                 // line 344
($context["row_num"] ?? null)) . "]"), (("field_" .                 // line 345
($context["row_num"] ?? null)) . "_4"), (($__internal_f769f712f3484f00110c86425acea59f5af2752239e2e8596bcb6effeb425b40 = $context["row"]) && is_array($__internal_f769f712f3484f00110c86425acea59f5af2752239e2e8596bcb6effeb425b40) || $__internal_f769f712f3484f00110c86425acea59f5af2752239e2e8596bcb6effeb425b40 instanceof ArrayAccess ? ($__internal_f769f712f3484f00110c86425acea59f5af2752239e2e8596bcb6effeb425b40["col_collation"] ?? null) : null), false);
                // line 347
                echo "
                            </td>
                            <td class=\"nowrap\" name=\"col_attribute\">
                                <span>";
                // line 350
                (((($__internal_98e944456c0f58b2585e4aa36e3a7e43f4b7c9038088f0f056004af41f4a007f = $context["row"]) && is_array($__internal_98e944456c0f58b2585e4aa36e3a7e43f4b7c9038088f0f056004af41f4a007f) || $__internal_98e944456c0f58b2585e4aa36e3a7e43f4b7c9038088f0f056004af41f4a007f instanceof ArrayAccess ? ($__internal_98e944456c0f58b2585e4aa36e3a7e43f4b7c9038088f0f056004af41f4a007f["col_attribute"] ?? null) : null)) ? (print (twig_escape_filter($this->env, (($__internal_a06a70691a7ca361709a372174fa669f5ee1c1e4ed302b3a5b61c10c80c02760 = $context["row"]) && is_array($__internal_a06a70691a7ca361709a372174fa669f5ee1c1e4ed302b3a5b61c10c80c02760) || $__internal_a06a70691a7ca361709a372174fa669f5ee1c1e4ed302b3a5b61c10c80c02760 instanceof ArrayAccess ? ($__internal_a06a70691a7ca361709a372174fa669f5ee1c1e4ed302b3a5b61c10c80c02760["col_attribute"] ?? null) : null), "html", null, true))) : (print ("")));
                echo "</span>
                                ";
                // line 351
                $this->loadTemplate("columns_definitions/column_attribute.twig", "database/central_columns/main.twig", 351)->display(twig_to_array(["column_number" =>                 // line 352
($context["row_num"] ?? null), "ci" => 5, "ci_offset" => 0, "extracted_columnspec" => [], "column_meta" => (($__internal_653499042eb14fd8415489ba6fa87c1e85cff03392e9f57b26d0da09b9be82ce =                 // line 356
$context["row"]) && is_array($__internal_653499042eb14fd8415489ba6fa87c1e85cff03392e9f57b26d0da09b9be82ce) || $__internal_653499042eb14fd8415489ba6fa87c1e85cff03392e9f57b26d0da09b9be82ce instanceof ArrayAccess ? ($__internal_653499042eb14fd8415489ba6fa87c1e85cff03392e9f57b26d0da09b9be82ce["col_attribute"] ?? null) : null), "submit_attribute" => false, "attribute_types" =>                 // line 358
($context["attribute_types"] ?? null)]));
                // line 360
                echo "                            </td>
                            <td class=\"nowrap\" name=\"col_isNull\">
                                <span>";
                // line 362
                echo twig_escape_filter($this->env, (((($__internal_ba9f0a3bb95c082f61c9fbf892a05514d732703d52edc77b51f2e6284135900b = $context["row"]) && is_array($__internal_ba9f0a3bb95c082f61c9fbf892a05514d732703d52edc77b51f2e6284135900b) || $__internal_ba9f0a3bb95c082f61c9fbf892a05514d732703d52edc77b51f2e6284135900b instanceof ArrayAccess ? ($__internal_ba9f0a3bb95c082f61c9fbf892a05514d732703d52edc77b51f2e6284135900b["col_isNull"] ?? null) : null)) ? (_gettext("Yes")) : (_gettext("No"))), "html", null, true);
                echo "</span>
                                ";
                // line 363
                $this->loadTemplate("columns_definitions/column_null.twig", "database/central_columns/main.twig", 363)->display(twig_to_array(["column_number" =>                 // line 364
($context["row_num"] ?? null), "ci" => 6, "ci_offset" => 0, "column_meta" => ["Null" => (($__internal_73db8eef4d2582468dab79a6b09c77ce3b48675a610afd65a1f325b68804a60c =                 // line 368
$context["row"]) && is_array($__internal_73db8eef4d2582468dab79a6b09c77ce3b48675a610afd65a1f325b68804a60c) || $__internal_73db8eef4d2582468dab79a6b09c77ce3b48675a610afd65a1f325b68804a60c instanceof ArrayAccess ? ($__internal_73db8eef4d2582468dab79a6b09c77ce3b48675a610afd65a1f325b68804a60c["col_isNull"] ?? null) : null)]]));
                // line 371
                echo "                            </td>
                            <td class=\"nowrap\" name=\"col_extra\">
                                <span>";
                // line 373
                echo twig_escape_filter($this->env, (($__internal_d8ad5934f1874c52fa2ac9a4dfae52038b39b8b03cfc82eeb53de6151d883972 = $context["row"]) && is_array($__internal_d8ad5934f1874c52fa2ac9a4dfae52038b39b8b03cfc82eeb53de6151d883972) || $__internal_d8ad5934f1874c52fa2ac9a4dfae52038b39b8b03cfc82eeb53de6151d883972 instanceof ArrayAccess ? ($__internal_d8ad5934f1874c52fa2ac9a4dfae52038b39b8b03cfc82eeb53de6151d883972["col_extra"] ?? null) : null), "html", null, true);
                echo "</span>
                                ";
                // line 374
                $this->loadTemplate("columns_definitions/column_extra.twig", "database/central_columns/main.twig", 374)->display(twig_to_array(["column_number" =>                 // line 375
($context["row_num"] ?? null), "ci" => 7, "ci_offset" => 0, "column_meta" => ["Extra" => (($__internal_df39c71428eaf37baa1ea2198679e0077f3699bdd31bb5ba10d084710b9da216 =                 // line 379
$context["row"]) && is_array($__internal_df39c71428eaf37baa1ea2198679e0077f3699bdd31bb5ba10d084710b9da216) || $__internal_df39c71428eaf37baa1ea2198679e0077f3699bdd31bb5ba10d084710b9da216 instanceof ArrayAccess ? ($__internal_df39c71428eaf37baa1ea2198679e0077f3699bdd31bb5ba10d084710b9da216["col_extra"] ?? null) : null)]]));
                // line 382
                echo "                            </td>
                        </tr>
                        ";
                // line 384
                $context["row_num"] = (($context["row_num"] ?? null) + 1);
                // line 385
                echo "                    ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['row'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 386
            echo "                </tbody>
            </table>
            ";
            // line 389
            echo "            ";
            $this->loadTemplate("select_all.twig", "database/central_columns/main.twig", 389)->display(twig_to_array(["pma_theme_image" =>             // line 390
($context["pmaThemeImage"] ?? null), "text_dir" =>             // line 391
($context["text_dir"] ?? null), "form_name" => "tableslistcontainer"]));
            // line 394
            echo "            ";
            echo PhpMyAdmin\Util::getButtonOrImage("edit_central_columns", "mult_submit change_central_columns", _gettext("Edit"), "b_edit", "edit central columns");
            // line 400
            echo "
            ";
            // line 401
            echo PhpMyAdmin\Util::getButtonOrImage("delete_central_columns", "mult_submit", _gettext("Delete"), "b_drop", "remove_from_central_columns");
            // line 407
            echo "
        </form>
    </div>
";
        }
    }

    public function getTemplateName()
    {
        return "database/central_columns/main.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  693 => 407,  691 => 401,  688 => 400,  685 => 394,  683 => 391,  682 => 390,  680 => 389,  676 => 386,  670 => 385,  668 => 384,  664 => 382,  662 => 379,  661 => 375,  660 => 374,  656 => 373,  652 => 371,  650 => 368,  649 => 364,  648 => 363,  644 => 362,  640 => 360,  638 => 358,  637 => 356,  636 => 352,  635 => 351,  631 => 350,  626 => 347,  624 => 345,  623 => 344,  622 => 343,  621 => 342,  620 => 341,  616 => 340,  612 => 338,  610 => 336,  609 => 335,  608 => 334,  607 => 331,  605 => 330,  601 => 328,  595 => 326,  593 => 325,  589 => 323,  587 => 321,  586 => 317,  585 => 316,  581 => 315,  577 => 313,  575 => 310,  574 => 307,  573 => 306,  569 => 305,  565 => 303,  563 => 301,  562 => 296,  561 => 292,  560 => 291,  556 => 290,  552 => 289,  546 => 286,  542 => 285,  537 => 283,  533 => 282,  529 => 281,  524 => 279,  520 => 278,  513 => 276,  506 => 272,  499 => 271,  497 => 270,  492 => 269,  490 => 268,  483 => 263,  481 => 262,  475 => 261,  471 => 259,  469 => 258,  463 => 257,  459 => 255,  457 => 254,  451 => 253,  447 => 251,  445 => 250,  439 => 249,  435 => 247,  433 => 246,  427 => 245,  423 => 243,  421 => 242,  415 => 241,  411 => 239,  409 => 238,  403 => 237,  399 => 235,  397 => 234,  391 => 233,  387 => 232,  382 => 230,  378 => 228,  375 => 227,  373 => 226,  364 => 220,  359 => 218,  356 => 217,  354 => 216,  345 => 210,  340 => 208,  336 => 206,  325 => 204,  321 => 203,  318 => 202,  316 => 201,  312 => 199,  308 => 197,  304 => 196,  299 => 194,  294 => 192,  288 => 188,  279 => 181,  275 => 180,  269 => 176,  261 => 171,  257 => 170,  253 => 169,  249 => 167,  246 => 166,  239 => 162,  235 => 161,  231 => 160,  227 => 158,  224 => 157,  216 => 152,  212 => 151,  208 => 150,  204 => 148,  202 => 147,  197 => 144,  193 => 142,  191 => 141,  188 => 140,  186 => 139,  174 => 129,  172 => 123,  168 => 121,  166 => 115,  162 => 113,  160 => 111,  159 => 104,  154 => 101,  152 => 96,  151 => 95,  150 => 94,  146 => 92,  144 => 90,  143 => 84,  139 => 82,  137 => 75,  133 => 73,  131 => 66,  127 => 64,  125 => 62,  124 => 54,  114 => 46,  112 => 45,  107 => 42,  105 => 41,  100 => 38,  98 => 37,  93 => 34,  91 => 33,  86 => 30,  84 => 29,  79 => 26,  77 => 25,  72 => 22,  70 => 21,  65 => 18,  63 => 17,  51 => 8,  46 => 6,  43 => 5,  39 => 4,  35 => 2,);
    }

    public function getSourceContext()
    {
        return new Source("", "database/central_columns/main.twig", "/home/files/phpmyadmin/release/phpMyAdmin-5.0+snapshot/templates/database/central_columns/main.twig");
    }
}

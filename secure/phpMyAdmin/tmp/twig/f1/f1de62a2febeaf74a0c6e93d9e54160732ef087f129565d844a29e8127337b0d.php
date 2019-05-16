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

/* columns_definitions/table_fields_definitions.twig */
class __TwigTemplate_d23636ef1e1d4b65e659c35a0d0e794faeac48ad872b50c31082b1b804a6d778 extends \Twig\Template
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
        echo "<div class=\"responsivetable\">
<table id=\"table_columns\" class=\"noclick\">
    <caption class=\"tblHeaders\">
        ";
        // line 4
        echo _gettext("Structure");
        // line 5
        echo "        ";
        echo PhpMyAdmin\Util::showMySQLDocu("CREATE_TABLE");
        echo "
    </caption>
    <tr>
        <th>
            ";
        // line 9
        echo _gettext("Name");
        // line 10
        echo "        </th>
        <th>
            ";
        // line 12
        echo _gettext("Type");
        // line 13
        echo "            ";
        echo PhpMyAdmin\Util::showMySQLDocu("data-types");
        echo "
        </th>
        <th>
            ";
        // line 16
        echo _gettext("Length/Values");
        // line 17
        echo "            ";
        echo PhpMyAdmin\Util::showHint(_gettext("If column type is \"enum\" or \"set\", please enter the values using this format: 'a','b','c'…<br>If you ever need to put a backslash (\"\") or a single quote (\"'\") amongst those values, precede it with a backslash (for example '\\\\xyz' or 'a\\'b')."));
        echo "
        </th>
        <th>
            ";
        // line 20
        echo _gettext("Default");
        // line 21
        echo "            ";
        echo PhpMyAdmin\Util::showHint(_gettext("For default values, please enter just a single value, without backslash escaping or quotes, using this format: a"));
        echo "
        </th>
        <th>
            ";
        // line 24
        echo _gettext("Collation");
        // line 25
        echo "        </th>
        <th>
            ";
        // line 27
        echo _gettext("Attributes");
        // line 28
        echo "        </th>
        <th>
            ";
        // line 30
        echo _gettext("Null");
        // line 31
        echo "        </th>

        ";
        // line 34
        echo "        ";
        if (((isset($context["change_column"]) || array_key_exists("change_column", $context)) &&  !twig_test_empty(($context["change_column"] ?? null)))) {
            // line 35
            echo "            <th>
                ";
            // line 36
            echo _gettext("Adjust privileges");
            // line 37
            echo "                ";
            echo PhpMyAdmin\Util::showDocu("faq", "faq6-39");
            echo "
            </th>
        ";
        }
        // line 40
        echo "
        ";
        // line 44
        echo "        ";
        if ( !($context["is_backup"] ?? null)) {
            // line 45
            echo "            <th>
                ";
            // line 46
            echo _gettext("Index");
            // line 47
            echo "            </th>
        ";
        }
        // line 49
        echo "
        <th>
            <abbr title=\"AUTO_INCREMENT\">A_I</abbr>
        </th>
        <th>
            ";
        // line 54
        echo _gettext("Comments");
        // line 55
        echo "        </th>

        ";
        // line 57
        if (($context["is_virtual_columns_supported"] ?? null)) {
            // line 58
            echo "            <th>
                ";
            // line 59
            echo _gettext("Virtuality");
            // line 60
            echo "            </th>
        ";
        }
        // line 62
        echo "
        ";
        // line 63
        if ((isset($context["fields_meta"]) || array_key_exists("fields_meta", $context))) {
            // line 64
            echo "            <th>
                ";
            // line 65
            echo _gettext("Move column");
            // line 66
            echo "            </th>
        ";
        }
        // line 68
        echo "
        ";
        // line 69
        if ((($context["mimework"] ?? null) && ($context["browse_mime"] ?? null))) {
            // line 70
            echo "            <th>
                ";
            // line 71
            echo _gettext("MIME type");
            // line 72
            echo "            </th>
            <th>
                <a href=\"transformation_overview.php";
            // line 75
            echo PhpMyAdmin\Url::getCommon();
            echo "#transformation\" title=\"";
            // line 76
            echo _gettext("List of available transformations and their options");
            // line 77
            echo "\" target=\"_blank\">
                    ";
            // line 78
            echo _gettext("Browser display transformation");
            // line 79
            echo "                </a>
            </th>
            <th>
                ";
            // line 82
            echo _gettext("Browser display transformation options");
            // line 83
            echo "                ";
            echo PhpMyAdmin\Util::showHint(_gettext("Please enter the values for transformation options using this format: 'a', 100, b,'c'…<br>If you ever need to put a backslash (\"\\\") or a single quote (\"'\") amongst those values, precede it with a backslash (for example '\\\\xyz' or 'a\\'b')."));
            echo "
            </th>
            <th>
                <a href=\"transformation_overview.php";
            // line 86
            echo PhpMyAdmin\Url::getCommon();
            echo "#input_transformation\"
                   title=\"";
            // line 87
            echo _gettext("List of available transformations and their options");
            echo "\"
                   target=\"_blank\">
                    ";
            // line 89
            echo _gettext("Input transformation");
            // line 90
            echo "                </a>
            </th>
            <th>
                ";
            // line 93
            echo _gettext("Input transformation options");
            // line 94
            echo "                ";
            echo PhpMyAdmin\Util::showHint(_gettext("Please enter the values for transformation options using this format: 'a', 100, b,'c'…<br>If you ever need to put a backslash (\"\\\") or a single quote (\"'\") amongst those values, precede it with a backslash (for example '\\\\xyz' or 'a\\'b')."));
            echo "
            </th>
        ";
        }
        // line 97
        echo "    </tr>
    ";
        // line 98
        $context["options"] = ["" => "", "VIRTUAL" => "VIRTUAL"];
        // line 99
        echo "    ";
        if ((($context["server_type"] ?? null) == "MariaDB")) {
            // line 100
            echo "        ";
            $context["options"] = twig_array_merge(($context["options"] ?? null), ["PERSISTENT" => "PERSISTENT"]);
            // line 101
            echo "        ";
            $context["options"] = twig_array_merge(($context["options"] ?? null), ["STORED" => "STORED"]);
            // line 102
            echo "    ";
        } else {
            // line 103
            echo "        ";
            $context["options"] = twig_array_merge(($context["options"] ?? null), ["STORED" => "STORED"]);
            // line 104
            echo "    ";
        }
        // line 105
        echo "    ";
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["content_cells"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["content_row"]) {
            // line 106
            echo "        <tr>
            ";
            // line 107
            $this->loadTemplate("columns_definitions/column_attributes.twig", "columns_definitions/table_fields_definitions.twig", 107)->display(twig_to_array(twig_array_merge($context["content_row"], ["options" =>             // line 108
($context["options"] ?? null), "change_column" =>             // line 109
($context["change_column"] ?? null), "is_virtual_columns_supported" =>             // line 110
($context["is_virtual_columns_supported"] ?? null), "browse_mime" =>             // line 111
($context["browse_mime"] ?? null), "max_rows" =>             // line 112
($context["max_rows"] ?? null), "char_editing" =>             // line 113
($context["char_editing"] ?? null), "attribute_types" =>             // line 114
($context["attribute_types"] ?? null), "privs_available" =>             // line 115
($context["privs_available"] ?? null), "max_length" =>             // line 116
($context["max_length"] ?? null), "dbi" =>             // line 117
($context["dbi"] ?? null), "disable_is" =>             // line 118
($context["disable_is"] ?? null)])));
            // line 120
            echo "        </tr>
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['content_row'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 122
        echo "</table>
</div>
";
    }

    public function getTemplateName()
    {
        return "columns_definitions/table_fields_definitions.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  283 => 122,  276 => 120,  274 => 118,  273 => 117,  272 => 116,  271 => 115,  270 => 114,  269 => 113,  268 => 112,  267 => 111,  266 => 110,  265 => 109,  264 => 108,  263 => 107,  260 => 106,  255 => 105,  252 => 104,  249 => 103,  246 => 102,  243 => 101,  240 => 100,  237 => 99,  235 => 98,  232 => 97,  225 => 94,  223 => 93,  218 => 90,  216 => 89,  211 => 87,  207 => 86,  200 => 83,  198 => 82,  193 => 79,  191 => 78,  188 => 77,  186 => 76,  183 => 75,  179 => 72,  177 => 71,  174 => 70,  172 => 69,  169 => 68,  165 => 66,  163 => 65,  160 => 64,  158 => 63,  155 => 62,  151 => 60,  149 => 59,  146 => 58,  144 => 57,  140 => 55,  138 => 54,  131 => 49,  127 => 47,  125 => 46,  122 => 45,  119 => 44,  116 => 40,  109 => 37,  107 => 36,  104 => 35,  101 => 34,  97 => 31,  95 => 30,  91 => 28,  89 => 27,  85 => 25,  83 => 24,  76 => 21,  74 => 20,  67 => 17,  65 => 16,  58 => 13,  56 => 12,  52 => 10,  50 => 9,  42 => 5,  40 => 4,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "columns_definitions/table_fields_definitions.twig", "/var/www/html/secure/phpMyAdmin/templates/columns_definitions/table_fields_definitions.twig");
    }
}

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

/* database/search/main.twig */
class __TwigTemplate_e5ef3daa7f898be424d7291d5f7a6316929547370725642c76f289673b8f4149 extends \Twig\Template
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
        echo "<a id=\"db_search\"></a>
<form id=\"db_search_form\" method=\"post\" action=\"db_search.php\" name=\"db_search\" class=\"ajax lock-page\">
    ";
        // line 3
        echo PhpMyAdmin\Url::getHiddenInputs(($context["db"] ?? null));
        echo "
    <fieldset>
        <legend>";
        // line 5
        echo _gettext("Search in database");
        echo "</legend>
        <p>
            <label for=\"criteriaSearchString\" class=\"displayblock\">
                ";
        // line 8
        echo _gettext("Words or values to search for (wildcard: \"%\"):");
        // line 9
        echo "            </label>
            <input id=\"criteriaSearchString\" name=\"criteriaSearchString\" class=\"all85\" type=\"text\" value=\"";
        // line 11
        echo twig_escape_filter($this->env, ($context["criteria_search_string"] ?? null), "html", null, true);
        echo "\">
        </p>

        <fieldset>
            <legend>";
        // line 15
        echo _gettext("Find:");
        echo "</legend>
            ";
        // line 17
        echo "            ";
        // line 19
        echo "            ";
        echo PhpMyAdmin\Util::getRadioFields("criteriaSearchType",         // line 21
($context["choices"] ?? null),         // line 22
($context["criteria_search_type"] ?? null), true, false);
        // line 25
        echo "
        </fieldset>

        <fieldset>
            <legend>";
        // line 29
        echo _gettext("Inside tables:");
        echo "</legend>
            <p>
                <a href=\"#\" onclick=\"setSelectOptions('db_search', 'criteriaTables[]', true); return false;\">
                    ";
        // line 32
        echo _gettext("Select all");
        // line 33
        echo "                </a> /
                <a href=\"#\" onclick=\"setSelectOptions('db_search', 'criteriaTables[]', false); return false;\">
                    ";
        // line 35
        echo _gettext("Unselect all");
        // line 36
        echo "                </a>
            </p>
            <select name=\"criteriaTables[]\" multiple>
                ";
        // line 39
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["tables_names_only"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["each_table"]) {
            // line 40
            echo "                    <option value=\"";
            echo twig_escape_filter($this->env, $context["each_table"], "html", null, true);
            echo "\"
                            ";
            // line 41
            if ((twig_length_filter($this->env, ($context["criteria_tables"] ?? null)) > 0)) {
                // line 42
                echo ((twig_in_filter($context["each_table"], ($context["criteria_tables"] ?? null))) ? (" selected") : (""));
                echo "
                            ";
            } else {
                // line 44
                echo " selected";
                echo "
                            ";
            }
            // line 46
            echo "                        >
                        ";
            // line 47
            echo twig_escape_filter($this->env, $context["each_table"], "html", null, true);
            echo "
                    </option>
                ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['each_table'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 50
        echo "            </select>
        </fieldset>

        <p>
            ";
        // line 55
        echo "            <label for=\"criteriaColumnName\" class=\"displayblock\">
                ";
        // line 56
        echo _gettext("Inside column:");
        // line 57
        echo "            </label>
            <input id=\"criteriaColumnName\" type=\"text\" name=\"criteriaColumnName\" class=\"all85\" value=\"";
        // line 59
        (( !twig_test_empty(($context["criteria_column_name"] ?? null))) ? (print (twig_escape_filter($this->env, ($context["criteria_column_name"] ?? null), "html", null, true))) : (print ("")));
        echo "\">
        </p>
    </fieldset>
    <fieldset class=\"tblFooters\">
        <input id=\"buttonGo\" class=\"btn btn-primary\" type=\"submit\" name=\"submit_search\" value=\"";
        // line 63
        echo _gettext("Go");
        echo "\">
    </fieldset>
</form>
<div id=\"togglesearchformdiv\">
    <a id=\"togglesearchformlink\"></a>
</div>
<div id=\"searchresults\"></div>
<div id=\"togglesearchresultsdiv\"><a id=\"togglesearchresultlink\"></a></div>
<br class=\"clearfloat\">
";
        // line 73
        echo "<div id=\"table-info\">
    <a id=\"table-link\" class=\"item\"></a>
</div>
";
        // line 77
        echo "<div id=\"browse-results\">
    ";
        // line 79
        echo "</div>
<div id=\"sqlqueryform\" class=\"clearfloat\">
    ";
        // line 82
        echo "</div>
";
        // line 84
        echo "<a id=\"togglequerybox\"></a>
";
    }

    public function getTemplateName()
    {
        return "database/search/main.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  179 => 84,  176 => 82,  172 => 79,  169 => 77,  164 => 73,  152 => 63,  145 => 59,  142 => 57,  140 => 56,  137 => 55,  131 => 50,  122 => 47,  119 => 46,  114 => 44,  109 => 42,  107 => 41,  102 => 40,  98 => 39,  93 => 36,  91 => 35,  87 => 33,  85 => 32,  79 => 29,  73 => 25,  71 => 22,  70 => 21,  68 => 19,  66 => 17,  62 => 15,  55 => 11,  52 => 9,  50 => 8,  44 => 5,  39 => 3,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "database/search/main.twig", "/home/files/phpmyadmin/release/phpMyAdmin-5.0+snapshot/templates/database/search/main.twig");
    }
}

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

/* create_tracking_version.twig */
class __TwigTemplate_b2144a32cbae2fd9e145256d470a07016bb4f9c1978d0e2c744ab7a37ad1c0b0 extends \Twig\Template
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
        echo "<div id=\"div_create_version\">
    <form method=\"post\" action=\"";
        // line 2
        echo ($context["url_query"] ?? null);
        echo "\">
        ";
        // line 3
        echo PhpMyAdmin\Url::getHiddenInputs(($context["db"] ?? null));
        echo "
        ";
        // line 4
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["selected"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["selected_table"]) {
            // line 5
            echo "            <input type=\"hidden\" name=\"selected[]\" value=\"";
            echo twig_escape_filter($this->env, $context["selected_table"], "html", null, true);
            echo "\">
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['selected_table'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 7
        echo "
        <fieldset>
            <legend>
                ";
        // line 10
        if ((twig_length_filter($this->env, ($context["selected"] ?? null)) == 1)) {
            // line 11
            echo "                    ";
            echo twig_escape_filter($this->env, sprintf(_gettext("Create version %1\$s of %2\$s"), (            // line 12
($context["last_version"] ?? null) + 1), ((            // line 13
($context["db"] ?? null) . ".") . (($__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4 = ($context["selected"] ?? null)) && is_array($__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4) || $__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4 instanceof ArrayAccess ? ($__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4[0] ?? null) : null))), "html", null, true);
            // line 14
            echo "
                ";
        } else {
            // line 16
            echo "                    ";
            echo twig_escape_filter($this->env, sprintf(_gettext("Create version %1\$s"), (($context["last_version"] ?? null) + 1)), "html", null, true);
            echo "
                ";
        }
        // line 18
        echo "            </legend>
            <input type=\"hidden\" name=\"version\" value=\"";
        // line 19
        echo twig_escape_filter($this->env, (($context["last_version"] ?? null) + 1), "html", null, true);
        echo "\">
            <p>";
        // line 20
        echo _gettext("Track these data definition statements:");
        echo "</p>

            ";
        // line 22
        if (((($context["type"] ?? null) == "both") || (($context["type"] ?? null) == "table"))) {
            // line 23
            echo "                <input type=\"checkbox\" name=\"alter_table\" value=\"true\"";
            // line 24
            echo ((twig_in_filter("ALTER TABLE", ($context["default_statements"] ?? null))) ? (" checked=\"checked\"") : (""));
            echo ">
                ALTER TABLE<br>
                <input type=\"checkbox\" name=\"rename_table\" value=\"true\"";
            // line 27
            echo ((twig_in_filter("RENAME TABLE", ($context["default_statements"] ?? null))) ? (" checked=\"checked\"") : (""));
            echo ">
                RENAME TABLE<br>
                <input type=\"checkbox\" name=\"create_table\" value=\"true\"";
            // line 30
            echo ((twig_in_filter("CREATE TABLE", ($context["default_statements"] ?? null))) ? (" checked=\"checked\"") : (""));
            echo ">
                CREATE TABLE<br>
                <input type=\"checkbox\" name=\"drop_table\" value=\"true\"";
            // line 33
            echo ((twig_in_filter("DROP TABLE", ($context["default_statements"] ?? null))) ? (" checked=\"checked\"") : (""));
            echo ">
                DROP TABLE<br>
            ";
        }
        // line 36
        echo "            ";
        if ((($context["type"] ?? null) == "both")) {
            // line 37
            echo "                <br>
            ";
        }
        // line 39
        echo "            ";
        if (((($context["type"] ?? null) == "both") || (($context["type"] ?? null) == "view"))) {
            // line 40
            echo "                <input type=\"checkbox\" name=\"alter_view\" value=\"true\"";
            // line 41
            echo ((twig_in_filter("ALTER VIEW", ($context["default_statements"] ?? null))) ? (" checked=\"checked\"") : (""));
            echo ">
                ALTER VIEW<br>
                <input type=\"checkbox\" name=\"create_view\" value=\"true\"";
            // line 44
            echo ((twig_in_filter("CREATE VIEW", ($context["default_statements"] ?? null))) ? (" checked=\"checked\"") : (""));
            echo ">
                CREATE VIEW<br>
                <input type=\"checkbox\" name=\"drop_view\" value=\"true\"";
            // line 47
            echo ((twig_in_filter("DROP VIEW", ($context["default_statements"] ?? null))) ? (" checked=\"checked\"") : (""));
            echo ">
                DROP VIEW<br>
            ";
        }
        // line 50
        echo "            <br>

            <input type=\"checkbox\" name=\"create_index\" value=\"true\"";
        // line 53
        echo ((twig_in_filter("CREATE INDEX", ($context["default_statements"] ?? null))) ? (" checked=\"checked\"") : (""));
        echo ">
            CREATE INDEX<br>
            <input type=\"checkbox\" name=\"drop_index\" value=\"true\"";
        // line 56
        echo ((twig_in_filter("DROP INDEX", ($context["default_statements"] ?? null))) ? (" checked=\"checked\"") : (""));
        echo ">
            DROP INDEX<br>

            <p>";
        // line 59
        echo _gettext("Track these data manipulation statements:");
        echo "</p>
            <input type=\"checkbox\" name=\"insert\" value=\"true\"";
        // line 61
        echo ((twig_in_filter("INSERT", ($context["default_statements"] ?? null))) ? (" checked=\"checked\"") : (""));
        echo ">
            INSERT<br>
            <input type=\"checkbox\" name=\"update\" value=\"true\"";
        // line 64
        echo ((twig_in_filter("UPDATE", ($context["default_statements"] ?? null))) ? (" checked=\"checked\"") : (""));
        echo ">
            UPDATE<br>
            <input type=\"checkbox\" name=\"delete\" value=\"true\"";
        // line 67
        echo ((twig_in_filter("DELETE", ($context["default_statements"] ?? null))) ? (" checked=\"checked\"") : (""));
        echo ">
            DELETE<br>
            <input type=\"checkbox\" name=\"truncate\" value=\"true\"";
        // line 70
        echo ((twig_in_filter("TRUNCATE", ($context["default_statements"] ?? null))) ? (" checked=\"checked\"") : (""));
        echo ">
            TRUNCATE<br>
        </fieldset>

        <fieldset class=\"tblFooters\">
            <input type=\"hidden\" name=\"submit_create_version\" value=\"1\">
            <input class=\"btn btn-primary\" type=\"submit\" value=\"";
        // line 76
        echo _gettext("Create version");
        echo "\">
        </fieldset>
    </form>
</div>
";
    }

    public function getTemplateName()
    {
        return "create_tracking_version.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  189 => 76,  180 => 70,  175 => 67,  170 => 64,  165 => 61,  161 => 59,  155 => 56,  150 => 53,  146 => 50,  140 => 47,  135 => 44,  130 => 41,  128 => 40,  125 => 39,  121 => 37,  118 => 36,  112 => 33,  107 => 30,  102 => 27,  97 => 24,  95 => 23,  93 => 22,  88 => 20,  84 => 19,  81 => 18,  75 => 16,  71 => 14,  69 => 13,  68 => 12,  66 => 11,  64 => 10,  59 => 7,  50 => 5,  46 => 4,  42 => 3,  38 => 2,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "create_tracking_version.twig", "/var/www/html/secure/phpMyAdmin/templates/create_tracking_version.twig");
    }
}

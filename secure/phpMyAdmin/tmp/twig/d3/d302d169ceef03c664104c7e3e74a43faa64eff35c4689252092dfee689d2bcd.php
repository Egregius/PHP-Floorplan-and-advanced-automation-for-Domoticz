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

/* table/tracking/report_table.twig */
class __TwigTemplate_f11aa70753a260ec50be58137f877781bbd143596849c75c20e5674905f2fd16 extends \Twig\Template
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
        echo "<table id=\"";
        echo twig_escape_filter($this->env, ($context["table_id"] ?? null), "html", null, true);
        echo "\" class=\"data\">
    <thead>
        <tr>
            <th>";
        // line 4
        echo _pgettext(        "Number", "#");
        echo "</th>
            <th>";
        // line 5
        echo _gettext("Date");
        echo "</th>
            <th>";
        // line 6
        echo _gettext("Username");
        echo "</th>
            <th>";
        // line 7
        echo twig_escape_filter($this->env, ($context["header_message"] ?? null), "html", null, true);
        echo "</th>
            <th>";
        // line 8
        echo _gettext("Action");
        echo "</th>
        </tr>
    </thead>
    <tbody>
        ";
        // line 12
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["entries"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["entry"]) {
            // line 13
            echo "            <tr class=\"noclick\">
                <td class=\"right\"><small>";
            // line 14
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["entry"], "line_number", [], "any", false, false, false, 14), "html", null, true);
            echo "</small></td>
                <td><small>";
            // line 15
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["entry"], "date", [], "any", false, false, false, 15), "html", null, true);
            echo "</small></td>
                <td><small>";
            // line 16
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["entry"], "username", [], "any", false, false, false, 16), "html", null, true);
            echo "</small></td>
                <td>";
            // line 17
            echo twig_get_attribute($this->env, $this->source, $context["entry"], "formated_statement", [], "any", false, false, false, 17);
            echo "</td>
                <td class=\"nowrap\">
                    <a class=\"delete_entry_anchor ajax\" href=\"tbl_tracking.php\" data-post=\"";
            // line 20
            echo twig_get_attribute($this->env, $this->source, $context["entry"], "url_params", [], "any", false, false, false, 20);
            echo "\">
                        ";
            // line 21
            echo ($context["drop_image_or_text"] ?? null);
            echo "
                    </a>
                </td>
            </tr>
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['entry'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 26
        echo "    </tbody>
</table>
";
    }

    public function getTemplateName()
    {
        return "table/tracking/report_table.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  104 => 26,  93 => 21,  89 => 20,  84 => 17,  80 => 16,  76 => 15,  72 => 14,  69 => 13,  65 => 12,  58 => 8,  54 => 7,  50 => 6,  46 => 5,  42 => 4,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "table/tracking/report_table.twig", "/home/files/phpmyadmin/release/phpMyAdmin-5.0+snapshot/templates/table/tracking/report_table.twig");
    }
}

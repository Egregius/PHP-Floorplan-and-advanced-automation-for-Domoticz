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

/* columns_definitions/move_column.twig */
class __TwigTemplate_2b3d699f5cfac6d2bab700c790e2c2a2dec6dae49b111c3b927be3edcafe3b80 extends \Twig\Template
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
        echo "<select id=\"field_";
        echo twig_escape_filter($this->env, ($context["column_number"] ?? null), "html", null, true);
        echo "_";
        echo twig_escape_filter($this->env, (($context["ci"] ?? null) - ($context["ci_offset"] ?? null)), "html", null, true);
        echo "\"
    name=\"field_move_to[";
        // line 2
        echo twig_escape_filter($this->env, ($context["column_number"] ?? null), "html", null, true);
        echo "]\"
    size=\"1\"
    width=\"5em\">
    <option value=\"\" selected=\"selected\">&nbsp;</option>
    <option value=\"-first\"";
        // line 6
        echo (((($context["current_index"] ?? null) == 0)) ? (" disabled=\"disabled\"") : (""));
        echo ">
        ";
        // line 7
        echo _gettext("first");
        // line 8
        echo "    </option>
    ";
        // line 9
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(range(0, (twig_length_filter($this->env, ($context["move_columns"] ?? null)) - 1)));
        foreach ($context['_seq'] as $context["_key"] => $context["mi"]) {
            // line 10
            echo "        <option value=\"";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (($__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4 = ($context["move_columns"] ?? null)) && is_array($__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4) || $__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4 instanceof ArrayAccess ? ($__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4[$context["mi"]] ?? null) : null), "name", [], "any", false, false, false, 10), "html", null, true);
            echo "\"";
            // line 11
            echo ((((($context["current_index"] ?? null) == $context["mi"]) || (($context["current_index"] ?? null) == ($context["mi"] + 1)))) ? (" disabled=\"disabled\"") : (""));
            echo ">
            ";
            // line 12
            echo twig_escape_filter($this->env, sprintf(_gettext("after %s"), PhpMyAdmin\Util::backquote(twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (($__internal_62824350bc4502ee19dbc2e99fc6bdd3bd90e7d8dd6e72f42c35efd048542144 = ($context["move_columns"] ?? null)) && is_array($__internal_62824350bc4502ee19dbc2e99fc6bdd3bd90e7d8dd6e72f42c35efd048542144) || $__internal_62824350bc4502ee19dbc2e99fc6bdd3bd90e7d8dd6e72f42c35efd048542144 instanceof ArrayAccess ? ($__internal_62824350bc4502ee19dbc2e99fc6bdd3bd90e7d8dd6e72f42c35efd048542144[$context["mi"]] ?? null) : null), "name", [], "any", false, false, false, 12)))), "html", null, true);
            echo "
        </option>
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['mi'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 15
        echo "</select>
";
    }

    public function getTemplateName()
    {
        return "columns_definitions/move_column.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  79 => 15,  70 => 12,  66 => 11,  62 => 10,  58 => 9,  55 => 8,  53 => 7,  49 => 6,  42 => 2,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "columns_definitions/move_column.twig", "/var/www/html/secure/phpMyAdmin/templates/columns_definitions/move_column.twig");
    }
}

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

/* table/browse_foreigners/show_all.twig */
class __TwigTemplate_219a852085798ab46f5bc050cee7350fe4522a133058031966dd96ddf40fa6d4 extends \Twig\Template
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
        if ((twig_test_iterable(twig_get_attribute($this->env, $this->source, ($context["foreign_data"] ?? null), "disp_row", [], "any", false, false, false, 1)) && (        // line 2
($context["show_all"] ?? null) && (twig_get_attribute($this->env, $this->source, ($context["foreign_data"] ?? null), "the_total", [], "any", false, false, false, 2) > ($context["max_rows"] ?? null))))) {
            // line 3
            echo "    <input class=\"btn btn-secondary\" type=\"submit\" id=\"foreign_showAll\" name=\"foreign_showAll\" value=\"";
            // line 4
            echo _gettext("Show all");
            echo "\">
";
        }
    }

    public function getTemplateName()
    {
        return "table/browse_foreigners/show_all.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  40 => 4,  38 => 3,  36 => 2,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "table/browse_foreigners/show_all.twig", "/home/files/phpmyadmin/release/phpMyAdmin-5.0+snapshot/templates/table/browse_foreigners/show_all.twig");
    }
}

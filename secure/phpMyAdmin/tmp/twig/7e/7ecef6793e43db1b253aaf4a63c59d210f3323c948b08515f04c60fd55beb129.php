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

/* database/central_columns/edit_table_header.twig */
class __TwigTemplate_46fdc62a55770eb8398414b34910185d80ce2ab5fb7b46aea81d0951d87b8b63 extends \Twig\Template
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
        echo "<table id=\"table_columns\" class=\"noclick\">
    <caption class=\"tblHeaders\">";
        // line 2
        echo _gettext("Structure");
        echo "</caption>
    <thead>
        <tr>
            ";
        // line 5
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["headers"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["header"]) {
            // line 6
            echo "                <th>";
            echo twig_escape_filter($this->env, $context["header"], "html", null, true);
            echo "</th>
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['header'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 8
        echo "        </tr>
    </thead>
";
    }

    public function getTemplateName()
    {
        return "database/central_columns/edit_table_header.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  57 => 8,  48 => 6,  44 => 5,  38 => 2,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "database/central_columns/edit_table_header.twig", "/home/files/phpmyadmin/release/phpMyAdmin-5.0+snapshot/templates/database/central_columns/edit_table_header.twig");
    }
}

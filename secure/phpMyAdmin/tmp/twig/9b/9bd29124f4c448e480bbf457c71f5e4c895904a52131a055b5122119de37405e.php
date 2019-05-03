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

/* test/add_data.twig */
class __TwigTemplate_0e1ea499f97c4aad7b63fa8442e4cfbd656e80505a092688ec462f451f3983d3 extends \Twig\Template
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
        echo twig_escape_filter($this->env, ($context["variable1"] ?? null), "html", null, true);
        echo "
";
        // line 2
        echo twig_escape_filter($this->env, ($context["variable2"] ?? null), "html", null, true);
        echo "
";
    }

    public function getTemplateName()
    {
        return "test/add_data.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  39 => 2,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "test/add_data.twig", "/home/files/phpmyadmin/release/phpMyAdmin-5.0+snapshot/templates/test/add_data.twig");
    }
}

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

/* sql/sql_query_results.twig */
class __TwigTemplate_5e063fb0d00097a60411ce189b2f8225beb1ce51ace8dfb7763e4cc4c4910c83 extends \Twig\Template
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
        echo ($context["table_maintenance"] ?? null);
        echo "
<div class=\"sqlqueryresults ajax\">
    ";
        // line 3
        echo ($context["previous_update_query"] ?? null);
        echo "
    ";
        // line 4
        echo ($context["profiling_chart"] ?? null);
        echo "
    ";
        // line 5
        echo ($context["missing_unique_column_message"] ?? null);
        echo "
    ";
        // line 6
        echo ($context["bookmark_created_message"] ?? null);
        echo "
    ";
        // line 7
        echo ($context["table"] ?? null);
        echo "
    ";
        // line 8
        echo ($context["indexes_problems"] ?? null);
        echo "
    ";
        // line 9
        echo ($context["bookmark_support"] ?? null);
        echo "
</div>
";
    }

    public function getTemplateName()
    {
        return "sql/sql_query_results.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  64 => 9,  60 => 8,  56 => 7,  52 => 6,  48 => 5,  44 => 4,  40 => 3,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "sql/sql_query_results.twig", "/var/www/home.egregius.be/secure/phpMyAdmin/templates/sql/sql_query_results.twig");
    }
}

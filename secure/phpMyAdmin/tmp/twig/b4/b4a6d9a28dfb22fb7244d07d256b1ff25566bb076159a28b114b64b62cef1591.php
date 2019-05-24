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

/* display/export/hidden_inputs.twig */
class __TwigTemplate_086811308da528cf8b782eb60e686adf055f7197cf39044167cb8f0cff5cca99 extends \Twig\Template
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
        if ((($context["export_type"] ?? null) == "server")) {
            // line 2
            echo "    ";
            echo PhpMyAdmin\Url::getHiddenInputs("", "", 1);
            echo "
";
        } elseif ((        // line 3
($context["export_type"] ?? null) == "database")) {
            // line 4
            echo "    ";
            echo PhpMyAdmin\Url::getHiddenInputs(($context["db"] ?? null), "", 1);
            echo "
";
        } else {
            // line 6
            echo "    ";
            echo PhpMyAdmin\Url::getHiddenInputs(($context["db"] ?? null), ($context["table"] ?? null), 1);
            echo "
";
        }
        // line 8
        echo "
";
        // line 10
        if ( !twig_test_empty(($context["single_table"] ?? null))) {
            // line 11
            echo "    <input type=\"hidden\" name=\"single_table\" value=\"TRUE\">
";
        }
        // line 13
        echo "
<input type=\"hidden\" name=\"export_type\" value=\"";
        // line 14
        echo twig_escape_filter($this->env, ($context["export_type"] ?? null), "html", null, true);
        echo "\">

";
        // line 17
        echo "<input type=\"hidden\" name=\"export_method\" value=\"";
        echo twig_escape_filter($this->env, ($context["export_method"] ?? null), "html", null, true);
        echo "\">

";
        // line 19
        if ( !twig_test_empty(($context["sql_query"] ?? null))) {
            // line 20
            echo "    <input type=\"hidden\" name=\"sql_query\" value=\"";
            echo twig_escape_filter($this->env, ($context["sql_query"] ?? null), "html", null, true);
            echo "\">
";
        }
        // line 22
        echo "
<input type=\"hidden\" name=\"template_id\" value=\"";
        // line 23
        echo twig_escape_filter($this->env, ($context["template_id"] ?? null), "html", null, true);
        echo "\">
";
    }

    public function getTemplateName()
    {
        return "display/export/hidden_inputs.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  90 => 23,  87 => 22,  81 => 20,  79 => 19,  73 => 17,  68 => 14,  65 => 13,  61 => 11,  59 => 10,  56 => 8,  50 => 6,  44 => 4,  42 => 3,  37 => 2,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "display/export/hidden_inputs.twig", "/var/www/html/secure/phpMyAdmin/templates/display/export/hidden_inputs.twig");
    }
}

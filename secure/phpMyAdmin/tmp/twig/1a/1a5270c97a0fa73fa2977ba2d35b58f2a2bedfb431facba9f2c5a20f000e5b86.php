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

/* columns_definitions/column_comment.twig */
class __TwigTemplate_5522d35e0174650142e1ae0bb4328cf09ec623e74452f527837718dad86c0620 extends \Twig\Template
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
        echo "<textarea id=\"field_";
        echo twig_escape_filter($this->env, ($context["column_number"] ?? null), "html", null, true);
        echo "_";
        echo twig_escape_filter($this->env, (($context["ci"] ?? null) - ($context["ci_offset"] ?? null)), "html", null, true);
        echo "\"
    rows=\"1\"
    name=\"field_comments[";
        // line 3
        echo twig_escape_filter($this->env, ($context["column_number"] ?? null), "html", null, true);
        echo "]\"
    maxlength=\"";
        // line 4
        echo twig_escape_filter($this->env, ($context["max_length"] ?? null), "html", null, true);
        echo "\">";
        // line 5
        echo ($context["value"] ?? null);
        // line 6
        echo "</textarea>
";
    }

    public function getTemplateName()
    {
        return "columns_definitions/column_comment.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  52 => 6,  50 => 5,  47 => 4,  43 => 3,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "columns_definitions/column_comment.twig", "/var/www/html/secure/phpMyAdmin/templates/columns_definitions/column_comment.twig");
    }
}

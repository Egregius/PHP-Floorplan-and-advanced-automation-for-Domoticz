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

/* preview_sql.twig */
class __TwigTemplate_ba2b0427bfad0779eea9dce0d4d2bb698d5bda4b1032edde8f99d41b9963d2a8 extends \Twig\Template
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
        echo "<div class=\"preview_sql\">
    ";
        // line 2
        if (twig_test_empty(($context["query_data"] ?? null))) {
            // line 3
            echo "        ";
            echo _gettext("No change");
            // line 4
            echo "    ";
        } elseif (twig_test_iterable(($context["query_data"] ?? null))) {
            // line 5
            echo "        ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["query_data"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["query"]) {
                // line 6
                echo "            ";
                echo PhpMyAdmin\Util::formatSql($context["query"]);
                echo "
        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['query'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 8
            echo "    ";
        } else {
            // line 9
            echo "        ";
            echo PhpMyAdmin\Util::formatSql(($context["query_data"] ?? null));
            echo "
    ";
        }
        // line 11
        echo "</div>
";
    }

    public function getTemplateName()
    {
        return "preview_sql.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  69 => 11,  63 => 9,  60 => 8,  51 => 6,  46 => 5,  43 => 4,  40 => 3,  38 => 2,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "preview_sql.twig", "/home/files/phpmyadmin/release/phpMyAdmin-5.0+snapshot/templates/preview_sql.twig");
    }
}

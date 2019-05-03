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

/* database/designer/page_selector.twig */
class __TwigTemplate_01b5b224d203d8d31b13fa1e5d3d9a633340056686b11d7f1c4d2a2c1278674a extends \Twig\Template
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
        echo "<select name=\"selected_page\" id=\"selected_page\">
    <option value=\"0\">-- ";
        // line 2
        echo _gettext("Select page");
        echo " --</option>
    ";
        // line 3
        if (($context["pdfwork"] ?? null)) {
            // line 4
            echo "        ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["pages"] ?? null));
            foreach ($context['_seq'] as $context["nr"] => $context["desc"]) {
                // line 5
                echo "            <option value=\"";
                echo twig_escape_filter($this->env, $context["nr"], "html", null, true);
                echo "\">
                ";
                // line 6
                echo twig_escape_filter($this->env, $context["desc"], "html", null, true);
                echo "
            </option>
        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['nr'], $context['desc'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 9
            echo "    ";
        }
        // line 10
        echo "</select>
";
    }

    public function getTemplateName()
    {
        return "database/designer/page_selector.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  66 => 10,  63 => 9,  54 => 6,  49 => 5,  44 => 4,  42 => 3,  38 => 2,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "database/designer/page_selector.twig", "/home/files/phpmyadmin/release/phpMyAdmin-5.0+snapshot/templates/database/designer/page_selector.twig");
    }
}

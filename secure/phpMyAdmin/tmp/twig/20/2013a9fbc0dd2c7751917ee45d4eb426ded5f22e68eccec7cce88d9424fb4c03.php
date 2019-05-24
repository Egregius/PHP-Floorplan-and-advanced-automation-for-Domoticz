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

/* display/export/options_output_charset.twig */
class __TwigTemplate_86e2e6c5e4dbd452c11a90d67b362cb525a0962280479548dcea4221494b8726 extends \Twig\Template
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
        echo "<li>
    <label for=\"select_charset\" class=\"desc\">
        ";
        // line 3
        echo _gettext("Character set of the file:");
        // line 4
        echo "    </label>
    <select id=\"select_charset\" name=\"charset\" size=\"1\">
        ";
        // line 6
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["encodings"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["charset"]) {
            // line 7
            echo "            <option value=\"";
            echo twig_escape_filter($this->env, $context["charset"], "html", null, true);
            echo "\"";
            // line 8
            if (((twig_test_empty(($context["export_charset"] ?? null)) && ($context["charset"] == "utf-8")) || (            // line 9
$context["charset"] == ($context["export_charset"] ?? null)))) {
                // line 10
                echo "                    selected";
            }
            // line 11
            echo ">";
            // line 12
            echo twig_escape_filter($this->env, $context["charset"], "html", null, true);
            // line 13
            echo "</option>
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['charset'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 15
        echo "    </select>
</li>
";
    }

    public function getTemplateName()
    {
        return "display/export/options_output_charset.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  70 => 15,  63 => 13,  61 => 12,  59 => 11,  56 => 10,  54 => 9,  53 => 8,  49 => 7,  45 => 6,  41 => 4,  39 => 3,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "display/export/options_output_charset.twig", "/var/www/html/secure/phpMyAdmin/templates/display/export/options_output_charset.twig");
    }
}

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

/* transformation_overview.twig */
class __TwigTemplate_53700b858d3c1f9396284cb021eecf9ad49121460b6dccb141c5c6ef80abdb05 extends \Twig\Template
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
        echo "<h2>";
        echo _gettext("Available MIME types");
        echo "</h2>

<ul>
  ";
        // line 4
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["mime_types"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["mime_type"]) {
            // line 5
            echo "    <li>
      ";
            // line 6
            echo ((twig_get_attribute($this->env, $this->source, $context["mime_type"], "is_empty", [], "any", false, false, false, 6)) ? ("<em>") : (""));
            echo "
      ";
            // line 7
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["mime_type"], "name", [], "any", false, false, false, 7), "html", null, true);
            echo "
      ";
            // line 8
            echo ((twig_get_attribute($this->env, $this->source, $context["mime_type"], "is_empty", [], "any", false, false, false, 8)) ? ("</em>") : (""));
            echo "
    </li>
  ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['mime_type'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 11
        echo "</ul>

<h2 id=\"transformation\">";
        // line 13
        echo _gettext("Available browser display transformations");
        echo "</h2>

<table>
  <thead>
    <tr>
      <th>";
        // line 18
        echo _gettext("Browser display transformation");
        echo "</th>
      <th>";
        // line 19
        echo _pgettext(        "for MIME transformation", "Description");
        echo "</th>
    </tr>
  </thead>
  <tbody>
    ";
        // line 23
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["transformations"] ?? null), "transformation", [], "any", false, false, false, 23));
        foreach ($context['_seq'] as $context["_key"] => $context["transformation"]) {
            // line 24
            echo "      <tr>
        <td>";
            // line 25
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["transformation"], "name", [], "any", false, false, false, 25), "html", null, true);
            echo "</td>
        <td>";
            // line 26
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["transformation"], "description", [], "any", false, false, false, 26), "html", null, true);
            echo "</td>
      </tr>
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['transformation'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 29
        echo "  </tbody>
</table>

<h2 id=\"input_transformation\">";
        // line 32
        echo _gettext("Available input transformations");
        echo "</h2>

<table>
  <thead>
    <tr>
      <th>";
        // line 37
        echo _gettext("Input transformation");
        echo "</th>
      <th>";
        // line 38
        echo _pgettext(        "for MIME transformation", "Description");
        echo "</th>
    </tr>
  </thead>
  <tbody>
    ";
        // line 42
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["transformations"] ?? null), "input_transformation", [], "any", false, false, false, 42));
        foreach ($context['_seq'] as $context["_key"] => $context["transformation"]) {
            // line 43
            echo "      <tr>
        <td>";
            // line 44
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["transformation"], "name", [], "any", false, false, false, 44), "html", null, true);
            echo "</td>
        <td>";
            // line 45
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["transformation"], "description", [], "any", false, false, false, 45), "html", null, true);
            echo "</td>
      </tr>
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['transformation'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 48
        echo "  </tbody>
</table>
";
    }

    public function getTemplateName()
    {
        return "transformation_overview.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  153 => 48,  144 => 45,  140 => 44,  137 => 43,  133 => 42,  126 => 38,  122 => 37,  114 => 32,  109 => 29,  100 => 26,  96 => 25,  93 => 24,  89 => 23,  82 => 19,  78 => 18,  70 => 13,  66 => 11,  57 => 8,  53 => 7,  49 => 6,  46 => 5,  42 => 4,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "transformation_overview.twig", "/home/files/phpmyadmin/release/phpMyAdmin-5.0+snapshot/templates/transformation_overview.twig");
    }
}

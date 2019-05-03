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

/* server/collations/index.twig */
class __TwigTemplate_90e151c39611517eaaa32fb98bb29f724eb9134afb066868ac0826fcb159544b extends \Twig\Template
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
        echo "<h2>
  ";
        // line 2
        echo PhpMyAdmin\Util::getImage("s_asci");
        echo "
  ";
        // line 3
        echo _gettext("Character sets and collations");
        // line 4
        echo "</h2>

<div id=\"div_mysql_charset_collations\">
  <table class=\"data noclick\">
    <thead>
      <tr>
        <th id=\"collationHeader\">";
        // line 10
        echo _gettext("Collation");
        echo "</th>
        <th>";
        // line 11
        echo _gettext("Description");
        echo "</th>
      </tr>
    </thead>

    ";
        // line 15
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["charsets"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["charset"]) {
            // line 16
            echo "      <tr>
        <th colspan=\"2\" class=\"right\">
          ";
            // line 18
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["charset"], "name", [], "any", false, false, false, 18), "html", null, true);
            echo "
          ";
            // line 19
            if ( !twig_test_empty(twig_get_attribute($this->env, $this->source, $context["charset"], "description", [], "any", false, false, false, 19))) {
                // line 20
                echo "            (<em>";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["charset"], "description", [], "any", false, false, false, 20), "html", null, true);
                echo "</em>)
          ";
            }
            // line 22
            echo "        </th>
      </tr>
      ";
            // line 24
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, $context["charset"], "collations", [], "any", false, false, false, 24));
            foreach ($context['_seq'] as $context["_key"] => $context["collation"]) {
                // line 25
                echo "        <tr";
                echo ((twig_get_attribute($this->env, $this->source, $context["collation"], "is_default", [], "any", false, false, false, 25)) ? (" class=\"marked\"") : (""));
                echo ">
          <td>";
                // line 26
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["collation"], "name", [], "any", false, false, false, 26), "html", null, true);
                echo "</td>
          <td>";
                // line 27
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["collation"], "description", [], "any", false, false, false, 27), "html", null, true);
                echo "</td>
        </tr>
      ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['collation'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 30
            echo "    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['charset'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 31
        echo "  </table>
</div>
";
    }

    public function getTemplateName()
    {
        return "server/collations/index.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  115 => 31,  109 => 30,  100 => 27,  96 => 26,  91 => 25,  87 => 24,  83 => 22,  77 => 20,  75 => 19,  71 => 18,  67 => 16,  63 => 15,  56 => 11,  52 => 10,  44 => 4,  42 => 3,  38 => 2,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "server/collations/index.twig", "/home/files/phpmyadmin/release/phpMyAdmin-5.0+snapshot/templates/server/collations/index.twig");
    }
}

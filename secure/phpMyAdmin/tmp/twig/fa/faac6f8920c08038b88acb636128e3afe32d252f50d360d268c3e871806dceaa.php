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

/* server/status/queries/index.twig */
class __TwigTemplate_5bf6444c1c57e023d65a4d05e1a3e2879ff4fbc3ece620ee1614f746c3d342e1 extends \Twig\Template
{
    private $source;

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'content' => [$this, 'block_content'],
        ];
    }

    protected function doGetParent(array $context)
    {
        // line 1
        return "server/status/base.twig";
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        // line 2
        $context["active"] = "queries";
        // line 1
        $this->parent = $this->loadTemplate("server/status/base.twig", "server/status/queries/index.twig", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = [])
    {
        // line 4
        echo "
";
        // line 5
        if (($context["is_data_loaded"] ?? null)) {
            // line 6
            echo "  <h3 id=\"serverstatusqueries\">
    ";
            // line 7
            // l10n: Questions is the name of a MySQL Status variable
            echo _gettext("Questions since startup:");
            // line 12
            echo "    ";
            echo twig_escape_filter($this->env, PhpMyAdmin\Util::formatNumber(twig_get_attribute($this->env, $this->source, ($context["stats"] ?? null), "total", [], "any", false, false, false, 12), 0), "html", null, true);
            echo "
    ";
            // line 13
            echo PhpMyAdmin\Util::showMySQLDocu("server-status-variables", false, null, null, "statvar_Questions");
            echo "
  </h3>

  <ul>
    <li>ø ";
            // line 17
            echo _gettext("per hour:");
            echo " ";
            echo twig_escape_filter($this->env, PhpMyAdmin\Util::formatNumber(twig_get_attribute($this->env, $this->source, ($context["stats"] ?? null), "per_hour", [], "any", false, false, false, 17), 0), "html", null, true);
            echo "</li>
    <li>ø ";
            // line 18
            echo _gettext("per minute:");
            echo " ";
            echo twig_escape_filter($this->env, PhpMyAdmin\Util::formatNumber(twig_get_attribute($this->env, $this->source, ($context["stats"] ?? null), "per_minute", [], "any", false, false, false, 18), 0), "html", null, true);
            echo "</li>
    ";
            // line 19
            if ((twig_get_attribute($this->env, $this->source, ($context["stats"] ?? null), "per_second", [], "any", false, false, false, 19) >= 1)) {
                // line 20
                echo "      <li>ø ";
                echo _gettext("per second:");
                echo " ";
                echo twig_escape_filter($this->env, PhpMyAdmin\Util::formatNumber(twig_get_attribute($this->env, $this->source, ($context["stats"] ?? null), "per_second", [], "any", false, false, false, 20), 0), "html", null, true);
                echo "</li>
    ";
            }
            // line 22
            echo "  </ul>

  <table id=\"serverstatusqueriesdetails\" class=\"width100 data sortable noclick\">
    <colgroup>
      <col class=\"namecol\">
      <col class=\"valuecol\" span=\"3\">
    </colgroup>

    <thead>
      <tr>
        <th>";
            // line 32
            echo _gettext("Statements");
            echo "</th>
        <th>";
            // line 33
            // l10n: # = Amount of queries
            echo _gettext("#");
            echo "</th>
        <th>ø ";
            // line 34
            echo _gettext("per hour");
            echo "</th>
        <th>%</th>
      </tr>
    </thead>

    <tbody>
      ";
            // line 40
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["queries"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["query"]) {
                // line 41
                echo "        <tr>
          <th class=\"name\">";
                // line 42
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["query"], "name", [], "any", false, false, false, 42), "html", null, true);
                echo "</th>
          <td class=\"value\">";
                // line 43
                echo twig_escape_filter($this->env, PhpMyAdmin\Util::formatNumber(twig_get_attribute($this->env, $this->source, $context["query"], "value", [], "any", false, false, false, 43), 5, 0, true), "html", null, true);
                echo "</td>
          <td class=\"value\">";
                // line 44
                echo twig_escape_filter($this->env, PhpMyAdmin\Util::formatNumber(twig_get_attribute($this->env, $this->source, $context["query"], "per_hour", [], "any", false, false, false, 44), 4, 1, true), "html", null, true);
                echo "</td>
          <td class=\"value\">";
                // line 45
                echo twig_escape_filter($this->env, PhpMyAdmin\Util::formatNumber(twig_get_attribute($this->env, $this->source, $context["query"], "percentage", [], "any", false, false, false, 45), 0, 2), "html", null, true);
                echo "</td>
        </tr>
      ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['query'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 48
            echo "    </tbody>
  </table>

  <div id=\"serverstatusquerieschart\" class=\"width100\" data-chart=\"";
            // line 51
            echo twig_escape_filter($this->env, json_encode(($context["chart"] ?? null)), "html", null, true);
            echo "\"></div>
";
        } else {
            // line 53
            echo "  ";
            echo call_user_func_array($this->env->getFilter('error')->getCallable(), [_gettext("Not enough privilege to view query statistics.")]);
            echo "
";
        }
        // line 55
        echo "
";
    }

    public function getTemplateName()
    {
        return "server/status/queries/index.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  169 => 55,  163 => 53,  158 => 51,  153 => 48,  144 => 45,  140 => 44,  136 => 43,  132 => 42,  129 => 41,  125 => 40,  116 => 34,  111 => 33,  107 => 32,  95 => 22,  87 => 20,  85 => 19,  79 => 18,  73 => 17,  66 => 13,  61 => 12,  58 => 7,  55 => 6,  53 => 5,  50 => 4,  47 => 3,  42 => 1,  40 => 2,  34 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "server/status/queries/index.twig", "/home/files/phpmyadmin/release/phpMyAdmin-5.0+snapshot/templates/server/status/queries/index.twig");
    }
}

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

/* server/status/processes/list.twig */
class __TwigTemplate_6c1d958e63a9739ed4ab9b4c2f7828074e35a9537e75f1f65e1b3102c17ecbd9 extends \Twig\Template
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
        echo "<div class=\"responsivetable\">
  <table id=\"tableprocesslist\" class=\"data clearfloat noclick sortable\">
    <thead>
      <tr>
        <th>";
        // line 5
        echo _gettext("Processes");
        echo "</th>
        ";
        // line 6
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["columns"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["column"]) {
            // line 7
            echo "          <th>
            <a href=\"server_status_processes.php\" data-post=\"";
            // line 8
            echo PhpMyAdmin\Url::getCommon(twig_get_attribute($this->env, $this->source, $context["column"], "params", [], "any", false, false, false, 8));
            echo "\" class=\"sortlink\">
              ";
            // line 9
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["column"], "name", [], "any", false, false, false, 9), "html", null, true);
            echo "
              ";
            // line 10
            if (twig_get_attribute($this->env, $this->source, $context["column"], "is_sorted", [], "any", false, false, false, 10)) {
                // line 11
                echo "                <img class=\"icon ic_s_desc soimg\" alt=\"";
                // line 12
                echo _gettext("Descending");
                echo "\" src=\"themes/dot.gif\" style=\"display: ";
                echo (((twig_get_attribute($this->env, $this->source, $context["column"], "sort_order", [], "any", false, false, false, 12) == "DESC")) ? ("none") : ("inline"));
                echo "\">
                <img class=\"icon ic_s_asc soimg hide\" alt=\"";
                // line 14
                echo _gettext("Ascending");
                echo "\" src=\"themes/dot.gif\" style=\"display: ";
                echo (((twig_get_attribute($this->env, $this->source, $context["column"], "sort_order", [], "any", false, false, false, 14) == "DESC")) ? ("inline") : ("none"));
                echo "\">
              ";
            }
            // line 16
            echo "            </a>
            ";
            // line 17
            if (twig_get_attribute($this->env, $this->source, $context["column"], "has_full_query", [], "any", false, false, false, 17)) {
                // line 18
                echo "              <a href=\"server_status_processes.php\" data-post=\"";
                echo PhpMyAdmin\Url::getCommon(($context["refresh_params"] ?? null), "");
                echo "\">
                ";
                // line 19
                if (twig_get_attribute($this->env, $this->source, $context["column"], "is_full", [], "any", false, false, false, 19)) {
                    // line 20
                    echo "                  ";
                    echo PhpMyAdmin\Util::getImage("s_partialtext", _gettext("Truncate shown queries"), ["class" => "icon_fulltext"]);
                    // line 24
                    echo "
                ";
                } else {
                    // line 26
                    echo "                  ";
                    echo PhpMyAdmin\Util::getImage("s_fulltext", _gettext("Show full queries"), ["class" => "icon_fulltext"]);
                    // line 30
                    echo "
                ";
                }
                // line 32
                echo "              </a>
            ";
            }
            // line 34
            echo "          </th>
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['column'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 36
        echo "      </tr>
    </thead>

    <tbody>
      ";
        // line 40
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["rows"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["row"]) {
            // line 41
            echo "        <tr>
          <td>
            <a class=\"ajax kill_process\" href=\"server_status_processes.php\" data-post=\"";
            // line 43
            echo PhpMyAdmin\Url::getCommon(["kill" => twig_get_attribute($this->env, $this->source, $context["row"], "id", [], "any", false, false, false, 43)], "");
            echo "\">
              ";
            // line 44
            echo _gettext("Kill");
            // line 45
            echo "            </a>
          </td>
          <td class=\"value\">";
            // line 47
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["row"], "id", [], "any", false, false, false, 47), "html", null, true);
            echo "</td>
          <td>";
            // line 48
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["row"], "user", [], "any", false, false, false, 48), "html", null, true);
            echo "</td>
          <td>";
            // line 49
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["row"], "host", [], "any", false, false, false, 49), "html", null, true);
            echo "</td>
          <td>
            ";
            // line 51
            if ((twig_get_attribute($this->env, $this->source, $context["row"], "db", [], "any", false, false, false, 51) != "")) {
                // line 52
                echo "              ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["row"], "db", [], "any", false, false, false, 52), "html", null, true);
                echo "
            ";
            } else {
                // line 54
                echo "              <em>";
                echo _gettext("None");
                echo "</em>
            ";
            }
            // line 56
            echo "          </td>
          <td>";
            // line 57
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["row"], "command", [], "any", false, false, false, 57), "html", null, true);
            echo "</td>
          <td class=\"value\">";
            // line 58
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["row"], "time", [], "any", false, false, false, 58), "html", null, true);
            echo "</td>
          <td>";
            // line 59
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["row"], "state", [], "any", false, false, false, 59), "html", null, true);
            echo "</td>
          <td>";
            // line 60
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["row"], "progress", [], "any", false, false, false, 60), "html", null, true);
            echo "</td>
          <td>";
            // line 61
            echo twig_get_attribute($this->env, $this->source, $context["row"], "info", [], "any", false, false, false, 61);
            echo "</td>
      ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['row'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 63
        echo "    </tbody>
  </table>
</div>
";
    }

    public function getTemplateName()
    {
        return "server/status/processes/list.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  192 => 63,  184 => 61,  180 => 60,  176 => 59,  172 => 58,  168 => 57,  165 => 56,  159 => 54,  153 => 52,  151 => 51,  146 => 49,  142 => 48,  138 => 47,  134 => 45,  132 => 44,  128 => 43,  124 => 41,  120 => 40,  114 => 36,  107 => 34,  103 => 32,  99 => 30,  96 => 26,  92 => 24,  89 => 20,  87 => 19,  82 => 18,  80 => 17,  77 => 16,  70 => 14,  64 => 12,  62 => 11,  60 => 10,  56 => 9,  52 => 8,  49 => 7,  45 => 6,  41 => 5,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "server/status/processes/list.twig", "/home/files/phpmyadmin/release/phpMyAdmin-5.0+snapshot/templates/server/status/processes/list.twig");
    }
}

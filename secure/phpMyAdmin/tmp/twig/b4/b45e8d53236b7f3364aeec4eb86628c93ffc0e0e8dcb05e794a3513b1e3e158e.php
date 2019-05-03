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

/* server/status/status/index.twig */
class __TwigTemplate_b64771729cacd48f56e417f5441e19db395bfa435f13799cf49600ae2f18e849 extends \Twig\Template
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
        $context["active"] = "status";
        // line 1
        $this->parent = $this->loadTemplate("server/status/base.twig", "server/status/status/index.twig", 1);
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
            echo "  <h3>";
            echo twig_escape_filter($this->env, sprintf(_gettext("Network traffic since startup: %s"), ($context["network_traffic"] ?? null)), "html", null, true);
            echo "</h3>
  <p>";
            // line 7
            echo twig_escape_filter($this->env, sprintf(_gettext("This MySQL server has been running for %1\$s. It started up on %2\$s."), ($context["uptime"] ?? null), ($context["start_time"] ?? null)), "html", null, true);
            echo "</p>

  <table id=\"serverstatustraffic\" class=\"width100 data noclick\">
    <thead>
      <tr>
        <th>
          ";
            // line 13
            echo _gettext("Traffic");
            // line 14
            echo "          ";
            echo PhpMyAdmin\Util::showHint(_gettext("On a busy server, the byte counters may overrun, so those statistics as reported by the MySQL server may be incorrect."));
            echo "
        </th>
        <th>#</th>
        <th>&oslash; ";
            // line 17
            echo _gettext("per hour");
            echo "</th>
      </tr>
    </thead>

    <tbody>
      ";
            // line 22
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["traffic"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["each_traffic"]) {
                // line 23
                echo "        <tr>
          <th class=\"name\">";
                // line 24
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["each_traffic"], "name", [], "any", false, false, false, 24), "html", null, true);
                echo "</th>
          <td class=\"value\">";
                // line 25
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["each_traffic"], "number", [], "any", false, false, false, 25), "html", null, true);
                echo "</td>
          <td class=\"value\">";
                // line 26
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["each_traffic"], "per_hour", [], "any", false, false, false, 26), "html", null, true);
                echo "</td>
        </tr>
      ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['each_traffic'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 29
            echo "    </tbody>
  </table>

  <table id=\"serverstatusconnections\" class=\"width100 data noclick\">
    <thead>
      <tr>
        <th>";
            // line 35
            echo _gettext("Connections");
            echo "</th>
        <th>#</th>
        <th>&oslash; ";
            // line 37
            echo _gettext("per hour");
            echo "</th>
        <th>%</th>
      </tr>
    </thead>

    <tbody>
      ";
            // line 43
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["connections"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["connection"]) {
                // line 44
                echo "        <tr>
          <th class=\"name\">";
                // line 45
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["connection"], "name", [], "any", false, false, false, 45), "html", null, true);
                echo "</th>
          <td class=\"value\">";
                // line 46
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["connection"], "number", [], "any", false, false, false, 46), "html", null, true);
                echo "</td>
          <td class=\"value\">";
                // line 47
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["connection"], "per_hour", [], "any", false, false, false, 47), "html", null, true);
                echo "</td>
          <td class=\"value\">";
                // line 48
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["connection"], "percentage", [], "any", false, false, false, 48), "html", null, true);
                echo "</td>
        </tr>
      ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['connection'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 51
            echo "    </tbody>
  </table>

  ";
            // line 54
            if ((($context["is_master"] ?? null) || ($context["is_slave"] ?? null))) {
                // line 55
                echo "    <p class=\"notice clearfloat\">
      ";
                // line 56
                if ((($context["is_master"] ?? null) && ($context["is_slave"] ?? null))) {
                    // line 57
                    echo "        ";
                    echo _gettext("This MySQL server works as <b>master</b> and <b>slave</b> in <b>replication</b> process.");
                    // line 58
                    echo "      ";
                } elseif (($context["is_master"] ?? null)) {
                    // line 59
                    echo "        ";
                    echo _gettext("This MySQL server works as <b>master</b> in <b>replication</b> process.");
                    // line 60
                    echo "      ";
                } elseif (($context["is_slave"] ?? null)) {
                    // line 61
                    echo "        ";
                    echo _gettext("This MySQL server works as <b>slave</b> in <b>replication</b> process.");
                    // line 62
                    echo "      ";
                }
                // line 63
                echo "    </p>

    <hr class=\"clearfloat\">

    <h3>
      <a name=\"replication\">";
                // line 68
                echo _gettext("Replication status");
                echo "</a>
    </h3>

    ";
                // line 71
                echo ($context["replication"] ?? null);
                echo "
  ";
            }
            // line 73
            echo "
";
        } else {
            // line 75
            echo "  ";
            echo call_user_func_array($this->env->getFilter('error')->getCallable(), [_gettext("Not enough privilege to view server status.")]);
            echo "
";
        }
        // line 77
        echo "
";
    }

    public function getTemplateName()
    {
        return "server/status/status/index.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  218 => 77,  212 => 75,  208 => 73,  203 => 71,  197 => 68,  190 => 63,  187 => 62,  184 => 61,  181 => 60,  178 => 59,  175 => 58,  172 => 57,  170 => 56,  167 => 55,  165 => 54,  160 => 51,  151 => 48,  147 => 47,  143 => 46,  139 => 45,  136 => 44,  132 => 43,  123 => 37,  118 => 35,  110 => 29,  101 => 26,  97 => 25,  93 => 24,  90 => 23,  86 => 22,  78 => 17,  71 => 14,  69 => 13,  60 => 7,  55 => 6,  53 => 5,  50 => 4,  47 => 3,  42 => 1,  40 => 2,  34 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "server/status/status/index.twig", "/home/files/phpmyadmin/release/phpMyAdmin-5.0+snapshot/templates/server/status/status/index.twig");
    }
}

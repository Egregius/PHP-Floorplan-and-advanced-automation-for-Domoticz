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

/* server/replication/status_table.twig */
class __TwigTemplate_ae0d40310fa4ab10e71a1c36b524d7d47d275447ec68e20fe054b5332c530a2a extends \Twig\Template
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
        echo "<div id=\"replication_";
        echo twig_escape_filter($this->env, ($context["type"] ?? null), "html", null, true);
        echo "_section\"";
        echo ((($context["is_hidden"] ?? null)) ? (" style=\"display: none;\"") : (""));
        echo ">
  ";
        // line 2
        if (($context["has_title"] ?? null)) {
            // line 3
            echo "    <h4>
      <a id=\"replication_";
            // line 4
            echo twig_escape_filter($this->env, ($context["type"] ?? null), "html", null, true);
            echo "\"></a>
      ";
            // line 5
            if ((($context["type"] ?? null) == "master")) {
                // line 6
                echo "        ";
                echo _gettext("Master status");
                // line 7
                echo "      ";
            } else {
                // line 8
                echo "        ";
                echo _gettext("Slave status");
                // line 9
                echo "      ";
            }
            // line 10
            echo "    </h4>
  ";
        }
        // line 12
        echo "
  <table id=\"server";
        // line 13
        echo twig_escape_filter($this->env, ($context["type"] ?? null), "html", null, true);
        echo "replicationsummary\" class=\"data\">
    <thead>
      <tr>
        <th>";
        // line 16
        echo _gettext("Variable");
        echo "</th>
        <th>";
        // line 17
        echo _gettext("Value");
        echo "</th>
      </tr>
    </thead>

    <tbody>
      ";
        // line 22
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["variables"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["variable"]) {
            // line 23
            echo "        <tr>
          <td class=\"name\">";
            // line 24
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["variable"], "name", [], "any", false, false, false, 24), "html", null, true);
            echo "</td>
          <td class=\"value\">
            <span";
            // line 26
            if ((twig_get_attribute($this->env, $this->source, $context["variable"], "status", [], "any", false, false, false, 26) == "attention")) {
                echo " class=\"attention\"";
            } elseif ((twig_get_attribute($this->env, $this->source, $context["variable"], "status", [], "any", false, false, false, 26) == "allfine")) {
                echo " class=\"allfine\"";
            }
            echo ">
              ";
            // line 27
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["variable"], "value", [], "any", false, false, false, 27), "html", null, true);
            echo "
            </span>
          </td>
        </tr>
      ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['variable'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 32
        echo "    </tbody>
  </table>
</div>
";
    }

    public function getTemplateName()
    {
        return "server/replication/status_table.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  121 => 32,  110 => 27,  102 => 26,  97 => 24,  94 => 23,  90 => 22,  82 => 17,  78 => 16,  72 => 13,  69 => 12,  65 => 10,  62 => 9,  59 => 8,  56 => 7,  53 => 6,  51 => 5,  47 => 4,  44 => 3,  42 => 2,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "server/replication/status_table.twig", "/home/files/phpmyadmin/release/phpMyAdmin-5.0+snapshot/templates/server/replication/status_table.twig");
    }
}

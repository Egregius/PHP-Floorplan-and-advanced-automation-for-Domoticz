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

/* server/status/processes/index.twig */
class __TwigTemplate_49e6c273bcaec177185bda456489c0054ecc94c9b633b2c4848d02ce2f9e042e extends \Twig\Template
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
        $context["active"] = "processes";
        // line 1
        $this->parent = $this->loadTemplate("server/status/base.twig", "server/status/processes/index.twig", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = [])
    {
        // line 4
        echo "
<fieldset id=\"tableFilter\">
  <legend>";
        // line 6
        echo _gettext("Filters");
        echo "</legend>
  <form action=\"server_status_processes.php\" method=\"post\">
    ";
        // line 8
        echo PhpMyAdmin\Url::getHiddenInputs(($context["url_params"] ?? null));
        echo "
    <input class=\"btn btn-secondary\" type=\"submit\" value=\"";
        // line 9
        echo _gettext("Refresh");
        echo "\">
    <div class=\"formelement\">
      <input type=\"checkbox\" name=\"showExecuting\" id=\"showExecuting\" class=\"autosubmit\"";
        // line 11
        echo ((($context["is_checked"] ?? null)) ? (" checked") : (""));
        echo ">
      <label for=\"showExecuting\">
        ";
        // line 13
        echo _gettext("Show only active");
        // line 14
        echo "      </label>
    </div>
  </form>
</fieldset>

";
        // line 19
        echo ($context["server_process_list"] ?? null);
        echo "

";
        // line 21
        echo call_user_func_array($this->env->getFilter('notice')->getCallable(), [_gettext("Note: Enabling the auto refresh here might cause heavy traffic between the web server and the MySQL server.")]);
        echo "

<div class=\"tabLinks\">
  <label>
    ";
        // line 25
        echo _gettext("Refresh rate");
        echo ":

    <select id=\"id_refreshRate\" class=\"refreshRate\" name=\"refreshRate\">
      ";
        // line 28
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable([0 => 2, 1 => 3, 2 => 4, 3 => 5, 4 => 10, 5 => 20, 6 => 40, 7 => 60, 8 => 120, 9 => 300, 10 => 600, 11 => 1200]);
        foreach ($context['_seq'] as $context["_key"] => $context["rate"]) {
            // line 29
            echo "        <option value=\"";
            echo twig_escape_filter($this->env, $context["rate"], "html", null, true);
            echo "\"";
            echo ((($context["rate"] == 5)) ? (" selected") : (""));
            echo ">
          ";
            // line 30
            if (($context["rate"] < 60)) {
                // line 31
                echo "            ";
                if (($context["rate"] == 1)) {
                    // line 32
                    echo "              ";
                    echo twig_escape_filter($this->env, sprintf(_gettext("%d second"), $context["rate"]), "html", null, true);
                    echo "
            ";
                } else {
                    // line 34
                    echo "              ";
                    echo twig_escape_filter($this->env, sprintf(_gettext("%d seconds"), $context["rate"]), "html", null, true);
                    echo "
            ";
                }
                // line 36
                echo "          ";
            } else {
                // line 37
                echo "            ";
                if ((($context["rate"] / 60) == 1)) {
                    // line 38
                    echo "              ";
                    echo twig_escape_filter($this->env, sprintf(_gettext("%d minute"), ($context["rate"] / 60)), "html", null, true);
                    echo "
            ";
                } else {
                    // line 40
                    echo "              ";
                    echo twig_escape_filter($this->env, sprintf(_gettext("%d minutes"), ($context["rate"] / 60)), "html", null, true);
                    echo "
            ";
                }
                // line 42
                echo "          ";
            }
            // line 43
            echo "        </option>
      ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['rate'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 45
        echo "    </select>
  </label>
  <a id=\"toggleRefresh\" href=\"#\">
    ";
        // line 48
        echo PhpMyAdmin\Util::getImage("play");
        echo "
    ";
        // line 49
        echo _gettext("Start auto refresh");
        // line 50
        echo "  </a>
</div>

";
    }

    public function getTemplateName()
    {
        return "server/status/processes/index.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  167 => 50,  165 => 49,  161 => 48,  156 => 45,  149 => 43,  146 => 42,  140 => 40,  134 => 38,  131 => 37,  128 => 36,  122 => 34,  116 => 32,  113 => 31,  111 => 30,  104 => 29,  100 => 28,  94 => 25,  87 => 21,  82 => 19,  75 => 14,  73 => 13,  68 => 11,  63 => 9,  59 => 8,  54 => 6,  50 => 4,  47 => 3,  42 => 1,  40 => 2,  34 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "server/status/processes/index.twig", "/home/files/phpmyadmin/release/phpMyAdmin-5.0+snapshot/templates/server/status/processes/index.twig");
    }
}

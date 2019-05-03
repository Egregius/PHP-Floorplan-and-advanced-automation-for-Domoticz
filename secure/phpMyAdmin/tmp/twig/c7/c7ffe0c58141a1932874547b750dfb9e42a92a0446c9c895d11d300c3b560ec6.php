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

/* server/status/advisor/index.twig */
class __TwigTemplate_23773c5b17663cce70e42cd0b5f3450561a36677c8468b79c4c943561faaa46c extends \Twig\Template
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
        $context["active"] = "advisor";
        // line 1
        $this->parent = $this->loadTemplate("server/status/base.twig", "server/status/advisor/index.twig", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 4
    public function block_content($context, array $blocks = [])
    {
        // line 5
        echo "
  ";
        // line 6
        if ( !twig_test_empty(($context["data"] ?? null))) {
            // line 7
            echo "    <a href=\"#openAdvisorInstructions\">";
            echo PhpMyAdmin\Util::getIcon("b_help", _gettext("Instructions"));
            echo "</a>

    <div id=\"statustabs_advisor\"></div>

    <div id=\"advisorInstructionsDialog\" class=\"hide\">
      <p>
        ";
            // line 13
            echo _gettext("The Advisor system can provide recommendations on server variables by analyzing the server status variables.");
            // line 16
            echo "      </p>
      <p>
        ";
            // line 18
            echo _gettext("Do note however that this system provides recommendations based on simple calculations and by rule of thumb which may not necessarily apply to your system.");
            // line 21
            echo "      </p>
      <p>
        ";
            // line 23
            echo _gettext("Prior to changing any of the configuration, be sure to know what you are changing (by reading the documentation) and how to undo the change. Wrong tuning can have a very negative effect on performance.");
            // line 26
            echo "      </p>
      <p>
        ";
            // line 28
            echo _gettext("The best way to tune your system would be to change only one setting at a time, observe or benchmark your database, and undo the change if there was no clearly measurable improvement.");
            // line 31
            echo "      </p>
    </div>

    <div id=\"advisorData\" class=\"hide\">";
            // line 34
            echo twig_escape_filter($this->env, ($context["data"] ?? null), "html", null, true);
            echo "</div>
  ";
        } else {
            // line 36
            echo "    ";
            echo call_user_func_array($this->env->getFilter('error')->getCallable(), [_gettext("Not enough privilege to view the advisor.")]);
            echo "
  ";
        }
        // line 38
        echo "
";
    }

    public function getTemplateName()
    {
        return "server/status/advisor/index.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  101 => 38,  95 => 36,  90 => 34,  85 => 31,  83 => 28,  79 => 26,  77 => 23,  73 => 21,  71 => 18,  67 => 16,  65 => 13,  55 => 7,  53 => 6,  50 => 5,  47 => 4,  42 => 1,  40 => 2,  34 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "server/status/advisor/index.twig", "/home/files/phpmyadmin/release/phpMyAdmin-5.0+snapshot/templates/server/status/advisor/index.twig");
    }
}

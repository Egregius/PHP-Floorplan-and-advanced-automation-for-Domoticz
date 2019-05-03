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

/* server/engines/show.twig */
class __TwigTemplate_8052d7394e98a03bba69f7348530c8871fe907a2388c06645351dabda3be9803 extends \Twig\Template
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
        echo PhpMyAdmin\Util::getImage("b_engine");
        echo "
  ";
        // line 3
        echo _gettext("Storage engines");
        // line 4
        echo "</h2>

";
        // line 6
        if ( !twig_test_empty(($context["engine"] ?? null))) {
            // line 7
            echo "  <h2>
    ";
            // line 8
            echo PhpMyAdmin\Util::getImage("b_engine");
            echo "
    ";
            // line 9
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["engine"] ?? null), "title", [], "any", false, false, false, 9), "html", null, true);
            echo "
    ";
            // line 10
            echo PhpMyAdmin\Util::showMySQLDocu(twig_get_attribute($this->env, $this->source, ($context["engine"] ?? null), "help_page", [], "any", false, false, false, 10));
            echo "
  </h2>
  <p><em>";
            // line 12
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["engine"] ?? null), "comment", [], "any", false, false, false, 12), "html", null, true);
            echo "</em></p>

  ";
            // line 14
            if (( !twig_test_empty(twig_get_attribute($this->env, $this->source, ($context["engine"] ?? null), "info_pages", [], "any", false, false, false, 14)) && twig_test_iterable(twig_get_attribute($this->env, $this->source, ($context["engine"] ?? null), "info_pages", [], "any", false, false, false, 14)))) {
                // line 15
                echo "    <p>
      <strong>[</strong>
      ";
                // line 17
                if (twig_test_empty(($context["page"] ?? null))) {
                    // line 18
                    echo "        <strong>";
                    echo _gettext("Variables");
                    echo "</strong>
      ";
                } else {
                    // line 20
                    echo "        <a href=\"server_engines.php";
                    // line 21
                    echo PhpMyAdmin\Url::getCommon(["engine" => twig_get_attribute($this->env, $this->source, ($context["engine"] ?? null), "engine", [], "any", false, false, false, 21)]);
                    echo "\">
          ";
                    // line 22
                    echo _gettext("Variables");
                    // line 23
                    echo "        </a>
      ";
                }
                // line 25
                echo "      ";
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["engine"] ?? null), "info_pages", [], "any", false, false, false, 25));
                foreach ($context['_seq'] as $context["current"] => $context["label"]) {
                    // line 26
                    echo "        <strong>|</strong>
        ";
                    // line 27
                    if (((isset($context["page"]) || array_key_exists("page", $context)) && (($context["page"] ?? null) == $context["current"]))) {
                        // line 28
                        echo "          <strong>";
                        echo twig_escape_filter($this->env, $context["label"], "html", null, true);
                        echo "</strong>
        ";
                    } else {
                        // line 30
                        echo "          <a href=\"server_engines.php";
                        // line 31
                        echo PhpMyAdmin\Url::getCommon(["engine" => twig_get_attribute($this->env, $this->source, ($context["engine"] ?? null), "engine", [], "any", false, false, false, 31), "page" => $context["current"]]);
                        echo "\">
            ";
                        // line 32
                        echo twig_escape_filter($this->env, $context["label"], "html", null, true);
                        echo "
          </a>
        ";
                    }
                    // line 35
                    echo "      ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['current'], $context['label'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 36
                echo "      <strong>]</strong>
    </p>
  ";
            }
            // line 39
            echo "
  ";
            // line 40
            if ( !twig_test_empty(twig_get_attribute($this->env, $this->source, ($context["engine"] ?? null), "page", [], "any", false, false, false, 40))) {
                // line 41
                echo "    ";
                echo twig_get_attribute($this->env, $this->source, ($context["engine"] ?? null), "page", [], "any", false, false, false, 41);
                echo "
  ";
            } else {
                // line 43
                echo "    <p>";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["engine"] ?? null), "support", [], "any", false, false, false, 43), "html", null, true);
                echo "</p>
    ";
                // line 44
                echo twig_get_attribute($this->env, $this->source, ($context["engine"] ?? null), "variables", [], "any", false, false, false, 44);
                echo "
  ";
            }
        } else {
            // line 47
            echo "  <p>";
            echo call_user_func_array($this->env->getFilter('error')->getCallable(), [_gettext("Unknown storage engine.")]);
            echo "</p>
";
        }
    }

    public function getTemplateName()
    {
        return "server/engines/show.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  158 => 47,  152 => 44,  147 => 43,  141 => 41,  139 => 40,  136 => 39,  131 => 36,  125 => 35,  119 => 32,  115 => 31,  113 => 30,  107 => 28,  105 => 27,  102 => 26,  97 => 25,  93 => 23,  91 => 22,  87 => 21,  85 => 20,  79 => 18,  77 => 17,  73 => 15,  71 => 14,  66 => 12,  61 => 10,  57 => 9,  53 => 8,  50 => 7,  48 => 6,  44 => 4,  42 => 3,  38 => 2,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "server/engines/show.twig", "/home/files/phpmyadmin/release/phpMyAdmin-5.0+snapshot/templates/server/engines/show.twig");
    }
}

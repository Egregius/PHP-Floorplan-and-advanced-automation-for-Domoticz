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

/* navigation/tree/path.twig */
class __TwigTemplate_c4a45b5a660e9e2bb24de7d11371c778c56cbd96c5658b3abc1ef17fc70da0fc extends \Twig\Template
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
        echo "<div class='list_container hide'>
  <ul";
        // line 2
        echo ((($context["has_search_results"] ?? null)) ? (" class=\"search_results\"") : (""));
        echo ">
    ";
        // line 3
        echo ($context["list_content"] ?? null);
        echo "
  </ul>

  ";
        // line 6
        if ( !($context["is_tree"] ?? null)) {
            // line 7
            echo "    <span class='hide loaded_db'>";
            echo twig_escape_filter($this->env, twig_urlencode_filter(($context["parent_name"] ?? null)), "html", null, true);
            echo "</span>
    ";
            // line 8
            if (twig_test_empty(($context["list_content"] ?? null))) {
                // line 9
                echo "      <div>";
                echo _gettext("No tables found in database.");
                echo "</div>
    ";
            }
            // line 11
            echo "  ";
        }
        // line 12
        echo "</div>
";
    }

    public function getTemplateName()
    {
        return "navigation/tree/path.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  66 => 12,  63 => 11,  57 => 9,  55 => 8,  50 => 7,  48 => 6,  42 => 3,  38 => 2,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "navigation/tree/path.twig", "/var/www/html/secure/phpMyAdmin/templates/navigation/tree/path.twig");
    }
}

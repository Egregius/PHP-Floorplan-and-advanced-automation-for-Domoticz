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

/* theme_preview.twig */
class __TwigTemplate_57b05380a760e3d0ae83ce76320c078b79cd7f7a29fc8eee3fc40c9ab8df55e7 extends \Twig\Template
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
        echo "<div class=\"theme_preview\">
    <h2>
        ";
        // line 3
        echo twig_escape_filter($this->env, ($context["name"] ?? null), "html", null, true);
        echo " (";
        echo twig_escape_filter($this->env, ($context["version"] ?? null), "html", null, true);
        echo ")
    </h2>
    <p>
        <a class=\"take_theme\" name=\"";
        // line 6
        echo twig_escape_filter($this->env, ($context["id"] ?? null), "html", null, true);
        echo "\" href=\"index.php";
        echo PhpMyAdmin\Url::getCommon(($context["url_params"] ?? null));
        echo "\">
            ";
        // line 7
        if ( !twig_test_empty(($context["screen"] ?? null))) {
            // line 8
            echo "                <img src=\"";
            echo twig_escape_filter($this->env, ($context["screen"] ?? null), "html", null, true);
            echo "\" alt=\"";
            echo twig_escape_filter($this->env, ($context["name"] ?? null), "html", null, true);
            echo "\" title=\"";
            echo twig_escape_filter($this->env, ($context["name"] ?? null), "html", null, true);
            echo "\">
                <br>
            ";
        } else {
            // line 11
            echo "                ";
            echo _gettext("No preview available.");
            // line 12
            echo "            ";
        }
        // line 13
        echo "            [ <strong>";
        echo _gettext("Take it");
        echo "</strong> ]
        </a>
    </p>
</div>
";
    }

    public function getTemplateName()
    {
        return "theme_preview.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  72 => 13,  69 => 12,  66 => 11,  55 => 8,  53 => 7,  47 => 6,  39 => 3,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "theme_preview.twig", "/home/files/phpmyadmin/release/phpMyAdmin-5.0+snapshot/templates/theme_preview.twig");
    }
}

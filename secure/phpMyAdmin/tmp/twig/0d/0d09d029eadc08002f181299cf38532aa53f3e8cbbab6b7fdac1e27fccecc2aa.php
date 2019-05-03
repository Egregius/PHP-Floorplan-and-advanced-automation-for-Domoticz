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

/* preferences/autoload.twig */
class __TwigTemplate_d5ba18350e3dabe7c8d92a77e20210cb3be33cc45424d81b17a0f57afd859f3e extends \Twig\Template
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
        echo "<div id=\"prefs_autoload\" class=\"notice print_ignore hide\">
    <form action=\"prefs_manage.php\" method=\"post\" class=\"disableAjax\">
        ";
        // line 3
        echo ($context["hidden_inputs"] ?? null);
        echo "
        <input type=\"hidden\" name=\"json\" value=\"\">
        <input type=\"hidden\" name=\"submit_import\" value=\"1\">
        <input type=\"hidden\" name=\"return_url\" value=\"";
        // line 6
        echo twig_escape_filter($this->env, ($context["return_url"] ?? null), "html", null, true);
        echo "\">
        ";
        // line 7
        echo _gettext("Your browser has phpMyAdmin configuration for this domain. Would you like to import it for current session?");
        // line 10
        echo "        <br>
        <a href=\"#yes\">";
        // line 11
        echo _gettext("Yes");
        echo "</a>
        / <a href=\"#no\">";
        // line 12
        echo _gettext("No");
        echo "</a>
        / <a href=\"#delete\">";
        // line 13
        echo _gettext("Delete settings");
        echo "</a>
    </form>
</div>
";
    }

    public function getTemplateName()
    {
        return "preferences/autoload.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  62 => 13,  58 => 12,  54 => 11,  51 => 10,  49 => 7,  45 => 6,  39 => 3,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "preferences/autoload.twig", "/home/files/phpmyadmin/release/phpMyAdmin-5.0+snapshot/templates/preferences/autoload.twig");
    }
}

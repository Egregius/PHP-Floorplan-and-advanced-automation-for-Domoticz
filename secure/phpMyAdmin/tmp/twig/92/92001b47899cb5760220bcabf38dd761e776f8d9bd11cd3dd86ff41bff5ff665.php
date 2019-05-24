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

/* display/export/format_dropdown.twig */
class __TwigTemplate_bcee1f8f0fbaa3f0bbb83171bc57b37731599dba3eb9dc491b309e0c66811dbe extends \Twig\Template
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
        echo "<div class=\"exportoptions\" id=\"format\">
    <h3>";
        // line 2
        echo _gettext("Format:");
        echo "</h3>
    ";
        // line 3
        echo ($context["dropdown"] ?? null);
        echo "
</div>
";
    }

    public function getTemplateName()
    {
        return "display/export/format_dropdown.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  42 => 3,  38 => 2,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "display/export/format_dropdown.twig", "/var/www/html/secure/phpMyAdmin/templates/display/export/format_dropdown.twig");
    }
}

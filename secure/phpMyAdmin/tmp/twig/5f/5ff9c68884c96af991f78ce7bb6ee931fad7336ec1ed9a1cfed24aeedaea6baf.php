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

/* navigation/tree/state.twig */
class __TwigTemplate_7bb86c58c822e739884958636d016b2441d83ea11eccb5cb7f0301ae0a4221f8 extends \Twig\Template
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
        echo ($context["quick_warp"] ?? null);
        echo "

<div class=\"clearfloat\"></div>

<ul>
  ";
        // line 6
        echo ($context["fast_filter"] ?? null);
        echo "
  ";
        // line 7
        echo ($context["controls"] ?? null);
        echo "
</ul>

";
        // line 10
        echo ($context["page_selector"] ?? null);
        echo "

<div id='pma_navigation_tree_content'>
  <ul>
    ";
        // line 14
        echo ($context["nodes"] ?? null);
        echo "
  </ul>
</div>
";
    }

    public function getTemplateName()
    {
        return "navigation/tree/state.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  60 => 14,  53 => 10,  47 => 7,  43 => 6,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "navigation/tree/state.twig", "/var/www/html/secure/phpMyAdmin/templates/navigation/tree/state.twig");
    }
}

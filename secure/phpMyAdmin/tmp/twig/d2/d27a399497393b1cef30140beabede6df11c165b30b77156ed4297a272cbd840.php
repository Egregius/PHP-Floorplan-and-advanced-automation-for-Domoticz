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

/* setup/error.twig */
class __TwigTemplate_85a73c60703436688c509d7d678ad78627401c30ceb7130fee4421c7c0c7eaf0 extends \Twig\Template
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
        echo "<div class=\"error\">
  <h4>";
        // line 2
        echo _gettext("Warning");
        echo "</h4>
  <p>";
        // line 3
        echo _gettext("Submitted form contains errors");
        echo "</p>
  <p>
    <a href=\"";
        // line 5
        echo PhpMyAdmin\Url::getCommon(twig_array_merge(($context["url_params"] ?? null), ["mode" => "revert"]));
        echo "\">
      ";
        // line 6
        echo _gettext("Try to revert erroneous fields to their default values");
        // line 7
        echo "    </a>
  </p>
</div>

";
        // line 11
        echo ($context["errors"] ?? null);
        echo "

<a class=\"btn\" href=\"index.php";
        // line 13
        echo PhpMyAdmin\Url::getCommon();
        echo "\">
  ";
        // line 14
        echo _gettext("Ignore errors");
        // line 15
        echo "</a>

<a class=\"btn\" href=\"";
        // line 17
        echo PhpMyAdmin\Url::getCommon(twig_array_merge(($context["url_params"] ?? null), ["mode" => "edit"]));
        echo "\">
  ";
        // line 18
        echo _gettext("Show form");
        // line 19
        echo "</a>
";
    }

    public function getTemplateName()
    {
        return "setup/error.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  80 => 19,  78 => 18,  74 => 17,  70 => 15,  68 => 14,  64 => 13,  59 => 11,  53 => 7,  51 => 6,  47 => 5,  42 => 3,  38 => 2,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "setup/error.twig", "/home/files/phpmyadmin/release/phpMyAdmin-5.0+snapshot/templates/setup/error.twig");
    }
}

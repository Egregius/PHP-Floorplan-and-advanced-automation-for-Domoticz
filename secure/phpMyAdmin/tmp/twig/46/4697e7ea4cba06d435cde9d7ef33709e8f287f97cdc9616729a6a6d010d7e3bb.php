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

/* login/twofactor/key-https-warning.twig */
class __TwigTemplate_a6bde99dcc5b99e09af34518010dc8672d5cd38f692aeafec0f466005094393e extends \Twig\Template
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
        if ( !($context["is_https"] ?? null)) {
            // line 2
            echo "<div class=\"error\">
";
            // line 3
            echo _gettext("You are not using https to access phpMyAdmin, therefore FIDO U2F device will most likely refuse to authenticate you.");
            // line 4
            echo "</div>
";
        }
    }

    public function getTemplateName()
    {
        return "login/twofactor/key-https-warning.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  42 => 4,  40 => 3,  37 => 2,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "login/twofactor/key-https-warning.twig", "/home/files/phpmyadmin/release/phpMyAdmin-5.0+snapshot/templates/login/twofactor/key-https-warning.twig");
    }
}

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

/* login/twofactor/simple.twig */
class __TwigTemplate_8e11e17495e71650a16f30d6bfe320fd81816844dd0b287caa9fa68df9007273 extends \Twig\Template
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
        echo "<input type=\"hidden\" name=\"2fa_confirm\" value=\"1\">
";
    }

    public function getTemplateName()
    {
        return "login/twofactor/simple.twig";
    }

    public function getDebugInfo()
    {
        return array (  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "login/twofactor/simple.twig", "/home/files/phpmyadmin/release/phpMyAdmin-5.0+snapshot/templates/login/twofactor/simple.twig");
    }
}

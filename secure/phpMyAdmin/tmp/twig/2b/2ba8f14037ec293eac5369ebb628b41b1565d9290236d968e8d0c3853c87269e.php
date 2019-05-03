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

/* test/gettext/pgettext.twig */
class __TwigTemplate_ae6f47919d0664910c5fc7b5127d74b02f0d2e94fd7c58c3f8400cb439d1adc0 extends \Twig\Template
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
        echo _pgettext(        "Text context", "Text");
    }

    public function getTemplateName()
    {
        return "test/gettext/pgettext.twig";
    }

    public function getDebugInfo()
    {
        return array (  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "test/gettext/pgettext.twig", "/home/files/phpmyadmin/release/phpMyAdmin-5.0+snapshot/templates/test/gettext/pgettext.twig");
    }
}

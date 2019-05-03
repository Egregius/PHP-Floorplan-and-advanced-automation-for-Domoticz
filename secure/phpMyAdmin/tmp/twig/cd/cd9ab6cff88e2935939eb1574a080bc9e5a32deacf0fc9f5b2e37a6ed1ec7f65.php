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

/* login/twofactor/key_configure.twig */
class __TwigTemplate_428b457e814fb6b7eaed876c38fc163554725faecad02f281af32f5c848d1235 extends \Twig\Template
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
        $this->loadTemplate("login/twofactor/key-https-warning.twig", "login/twofactor/key_configure.twig", 1)->display($context);
        // line 2
        echo "<p>
";
        // line 3
        echo _gettext("Please connect your FIDO U2F device into your computer's USB port. Then confirm registration on the device.");
        // line 4
        echo "</p>
<input id=\"u2f_registration_response\" name=\"u2f_registration_response\" value=\"\" type=\"hidden\" data-request=\"";
        // line 5
        echo twig_escape_filter($this->env, ($context["request"] ?? null), "html", null, true);
        echo "\" data-signatures=\"";
        echo twig_escape_filter($this->env, ($context["signatures"] ?? null), "html", null, true);
        echo "\">
";
    }

    public function getTemplateName()
    {
        return "login/twofactor/key_configure.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  45 => 5,  42 => 4,  40 => 3,  37 => 2,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "login/twofactor/key_configure.twig", "/home/files/phpmyadmin/release/phpMyAdmin-5.0+snapshot/templates/login/twofactor/key_configure.twig");
    }
}

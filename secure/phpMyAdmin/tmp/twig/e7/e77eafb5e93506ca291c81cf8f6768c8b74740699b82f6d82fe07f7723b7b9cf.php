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

/* login/twofactor/application.twig */
class __TwigTemplate_c2e4816344c43dbbe8d14f19b6a589bcb04e02dd92a554248b6684f209307a72 extends \Twig\Template
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
        echo "<p>
<label>";
        // line 2
        echo _gettext("Authentication code:");
        echo " <input type=\"text\" name=\"2fa_code\" autocomplete=\"off\"></label>
</p>
<p>";
        // line 4
        echo _gettext("Open the two-factor authentication app on your device to view your authentication code and verify your identity.");
        echo "</p>
";
    }

    public function getTemplateName()
    {
        return "login/twofactor/application.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  43 => 4,  38 => 2,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "login/twofactor/application.twig", "/home/files/phpmyadmin/release/phpMyAdmin-5.0+snapshot/templates/login/twofactor/application.twig");
    }
}

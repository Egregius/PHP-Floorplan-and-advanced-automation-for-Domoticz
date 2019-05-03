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

/* login/twofactor/application_configure.twig */
class __TwigTemplate_ee57982157641f2ddbff165ee0e6ea13b4ae632329a7257f4a4516b058ae2bf8 extends \Twig\Template
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
        echo PhpMyAdmin\Url::getHiddenInputs();
        echo "
<p>
    ";
        // line 3
        echo _gettext("Please scan following QR code into the two-factor authentication app on your device and enter authentication code it generates.");
        // line 4
        echo "</p>
<p>
    ";
        // line 6
        if (($context["has_imagick"] ?? null)) {
            // line 7
            echo "        <img src=\"";
            echo twig_escape_filter($this->env, ($context["image"] ?? null), "html", null, true);
            echo "\">
    ";
        } else {
            // line 9
            echo "        ";
            echo ($context["image"] ?? null);
            echo "
    ";
        }
        // line 11
        echo "</p>
<p>
    ";
        // line 13
        echo _gettext("Secret/key:");
        echo " <strong>";
        echo twig_escape_filter($this->env, ($context["secret"] ?? null), "html", null, true);
        echo "</strong>
</p>
<p>
    <label>";
        // line 16
        echo _gettext("Authentication code:");
        echo " <input type=\"text\" name=\"2fa_code\" autocomplete=\"off\"></label>
</p>
";
    }

    public function getTemplateName()
    {
        return "login/twofactor/application_configure.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  72 => 16,  64 => 13,  60 => 11,  54 => 9,  48 => 7,  46 => 6,  42 => 4,  40 => 3,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "login/twofactor/application_configure.twig", "/home/files/phpmyadmin/release/phpMyAdmin-5.0+snapshot/templates/login/twofactor/application_configure.twig");
    }
}

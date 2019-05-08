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

/* login/footer.twig */
class __TwigTemplate_5cb7914b383b698dffbe5234a22ae4eacf5328527ca92b883a98e72e34b32b31 extends \Twig\Template
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
        echo "</div>
";
        // line 2
        if ((($context["check_timeout"] ?? null) == true)) {
            // line 3
            echo "    </div>
";
        }
    }

    public function getTemplateName()
    {
        return "login/footer.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  40 => 3,  38 => 2,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "login/footer.twig", "/var/www/html/secure/phpMyAdmin/templates/login/footer.twig");
    }
}

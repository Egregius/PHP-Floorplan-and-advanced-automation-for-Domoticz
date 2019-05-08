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

/* footer.twig */
class __TwigTemplate_3d74c552cef4e4bd59bfb3b5ae564bf3ad9b62689aaf2621870ba03b009e7df5 extends \Twig\Template
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
        if ( !($context["is_ajax"] ?? null)) {
            // line 2
            echo "  </div>
";
        }
        // line 4
        if (( !($context["is_ajax"] ?? null) &&  !($context["is_minimal"] ?? null))) {
            // line 5
            echo "  ";
            echo ($context["self_link"] ?? null);
            echo "

  <div class=\"clearfloat\" id=\"pma_errors\">
    ";
            // line 8
            echo ($context["error_messages"] ?? null);
            echo "
  </div>

  ";
            // line 11
            echo ($context["scripts"] ?? null);
            echo "

  ";
            // line 13
            if (($context["is_demo"] ?? null)) {
                // line 14
                echo "    <div id=\"pma_demo\">
      ";
                // line 15
                echo ($context["demo_message"] ?? null);
                echo "
    </div>
  ";
            }
            // line 18
            echo "
  ";
            // line 19
            echo ($context["footer"] ?? null);
            echo "
";
        }
        // line 21
        if ( !($context["is_ajax"] ?? null)) {
            // line 22
            echo "  </body>
</html>
";
        }
    }

    public function getTemplateName()
    {
        return "footer.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  82 => 22,  80 => 21,  75 => 19,  72 => 18,  66 => 15,  63 => 14,  61 => 13,  56 => 11,  50 => 8,  43 => 5,  41 => 4,  37 => 2,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "footer.twig", "/var/www/html/secure/phpMyAdmin/templates/footer.twig");
    }
}

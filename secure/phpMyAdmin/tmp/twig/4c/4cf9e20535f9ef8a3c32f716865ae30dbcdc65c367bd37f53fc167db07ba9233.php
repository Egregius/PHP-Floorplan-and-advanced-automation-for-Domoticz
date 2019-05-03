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

/* display/results/table.twig */
class __TwigTemplate_553ca3ef20f75f0d67b0ec824503684f2c6de28be9dc1acdd40e5b116a318a65 extends \Twig\Template
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
        echo ($context["sql_query_message"] ?? null);
        echo "
";
        // line 2
        echo ($context["navigation"] ?? null);
        echo "
";
        // line 3
        echo ($context["headers"] ?? null);
        echo "
<tbody>
    ";
        // line 5
        echo ($context["body"] ?? null);
        echo "
</tbody>
</table>
</div>
";
        // line 9
        echo ($context["multi_row_operation_links"] ?? null);
        echo "
";
        // line 10
        echo ($context["navigation"] ?? null);
        echo "
";
        // line 11
        echo ($context["operations"] ?? null);
        echo "
";
    }

    public function getTemplateName()
    {
        return "display/results/table.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  63 => 11,  59 => 10,  55 => 9,  48 => 5,  43 => 3,  39 => 2,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "display/results/table.twig", "/var/www/home.egregius.be/secure/phpMyAdmin/templates/display/results/table.twig");
    }
}

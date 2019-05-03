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

/* database/qbe/footer_options.twig */
class __TwigTemplate_e2c967bd64f3bcc9b49aeb65df9c6c6db8627d562accea75dcd5e4dbf0f7e09d extends \Twig\Template
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
        echo "<div class=\"floatleft\">
    ";
        // line 2
        if ((($context["type"] ?? null) == "row")) {
            // line 3
            echo "        ";
            echo _gettext("Add/Delete criteria rows");
            echo ":
    ";
        } else {
            // line 5
            echo "        ";
            echo _gettext("Add/Delete columns");
            echo ":
    ";
        }
        // line 7
        echo "    <select size=\"1\" name=\"";
        echo (((($context["type"] ?? null) == "row")) ? ("criteriaRowAdd") : ("criteriaColumnAdd"));
        echo "\">
        <option value=\"-3\">-3</option>
        <option value=\"-2\">-2</option>
        <option value=\"-1\">-1</option>
        <option value=\"0\" selected=\"selected\">0</option>
        <option value=\"1\">1</option>
        <option value=\"2\">2</option>
        <option value=\"3\">3</option>
    </select>
</div>
";
    }

    public function getTemplateName()
    {
        return "database/qbe/footer_options.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  52 => 7,  46 => 5,  40 => 3,  38 => 2,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "database/qbe/footer_options.twig", "/home/files/phpmyadmin/release/phpMyAdmin-5.0+snapshot/templates/database/qbe/footer_options.twig");
    }
}

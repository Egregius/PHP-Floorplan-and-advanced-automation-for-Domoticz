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

/* columns_definitions/column_type.twig */
class __TwigTemplate_4b153192ce6147c184ec492316f9660b258581d4ef1c109aa248488bdebb6193 extends \Twig\Template
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
        echo "<select class=\"column_type\"
    name=\"field_type[";
        // line 2
        echo twig_escape_filter($this->env, ($context["column_number"] ?? null), "html", null, true);
        echo "]\"
    id=\"field_";
        // line 3
        echo twig_escape_filter($this->env, ($context["column_number"] ?? null), "html", null, true);
        echo "_";
        echo twig_escape_filter($this->env, (($context["ci"] ?? null) - ($context["ci_offset"] ?? null)), "html", null, true);
        echo "\"";
        // line 4
        if ((twig_get_attribute($this->env, $this->source, ($context["column_meta"] ?? null), "column_status", [], "array", true, true, false, 4) &&  !(($__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4 = (($__internal_62824350bc4502ee19dbc2e99fc6bdd3bd90e7d8dd6e72f42c35efd048542144 = ($context["column_meta"] ?? null)) && is_array($__internal_62824350bc4502ee19dbc2e99fc6bdd3bd90e7d8dd6e72f42c35efd048542144) || $__internal_62824350bc4502ee19dbc2e99fc6bdd3bd90e7d8dd6e72f42c35efd048542144 instanceof ArrayAccess ? ($__internal_62824350bc4502ee19dbc2e99fc6bdd3bd90e7d8dd6e72f42c35efd048542144["column_status"] ?? null) : null)) && is_array($__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4) || $__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4 instanceof ArrayAccess ? ($__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4["isEditable"] ?? null) : null))) {
            // line 5
            echo "disabled=\"disabled\"";
        }
        // line 6
        echo ">
    ";
        // line 7
        echo PhpMyAdmin\Util::getSupportedDatatypes(true, ($context["type_upper"] ?? null));
        echo "
</select>
";
    }

    public function getTemplateName()
    {
        return "columns_definitions/column_type.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  55 => 7,  52 => 6,  49 => 5,  47 => 4,  42 => 3,  38 => 2,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "columns_definitions/column_type.twig", "/var/www/html/secure/phpMyAdmin/templates/columns_definitions/column_type.twig");
    }
}

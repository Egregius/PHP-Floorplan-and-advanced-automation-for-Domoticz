<?php

/* database/search/result_divs.twig */
class __TwigTemplate_fe892cb6170e2131a4f5055bda4d7bb80af2b86e24c012d7fa8c862ff71e13a1 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        // line 2
        echo "<div id=\"table-info\">
    <a id=\"table-link\" class=\"item\"></a>
</div>
";
        // line 6
        echo "<div id=\"browse-results\">
    ";
        // line 8
        echo "</div>
<div id=\"sqlqueryform\" class=\"clearfloat\">
    ";
        // line 11
        echo "</div>
";
        // line 13
        echo "<a id=\"togglequerybox\"></a>
";
    }

    public function getTemplateName()
    {
        return "database/search/result_divs.twig";
    }

    public function getDebugInfo()
    {
        return array (  34 => 13,  31 => 11,  27 => 8,  24 => 6,  19 => 2,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "database/search/result_divs.twig", "/var/www/home.egregius.be/secure/phpMyAdmin/templates/database/search/result_divs.twig");
    }
}

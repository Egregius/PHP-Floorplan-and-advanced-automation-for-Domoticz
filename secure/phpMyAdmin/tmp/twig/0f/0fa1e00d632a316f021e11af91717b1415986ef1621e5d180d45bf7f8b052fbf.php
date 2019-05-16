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

/* columns_definitions/column_length.twig */
class __TwigTemplate_ecbfca257b370ae3b5e209903b3b09e1397c7cbae251318700395663be71ff0a extends \Twig\Template
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
        echo "<input id=\"field_";
        echo twig_escape_filter($this->env, ($context["column_number"] ?? null), "html", null, true);
        echo "_";
        echo twig_escape_filter($this->env, (($context["ci"] ?? null) - ($context["ci_offset"] ?? null)), "html", null, true);
        echo "\"
    type=\"text\"
    name=\"field_length[";
        // line 3
        echo twig_escape_filter($this->env, ($context["column_number"] ?? null), "html", null, true);
        echo "]\"
    size=\"";
        // line 4
        echo twig_escape_filter($this->env, ($context["length_values_input_size"] ?? null), "html", null, true);
        echo "\"
    value=\"";
        // line 5
        echo twig_escape_filter($this->env, ($context["length_to_display"] ?? null), "html", null, true);
        echo "\"
    class=\"textfield\">
<p class=\"enum_notice\" id=\"enum_notice_";
        // line 7
        echo twig_escape_filter($this->env, ($context["column_number"] ?? null), "html", null, true);
        echo "_";
        echo twig_escape_filter($this->env, (($context["ci"] ?? null) - ($context["ci_offset"] ?? null)), "html", null, true);
        echo "\">
    <a href=\"#\" class=\"open_enum_editor\">
        ";
        // line 9
        echo _gettext("Edit ENUM/SET values");
        // line 10
        echo "    </a>
</p>
";
    }

    public function getTemplateName()
    {
        return "columns_definitions/column_length.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  65 => 10,  63 => 9,  56 => 7,  51 => 5,  47 => 4,  43 => 3,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "columns_definitions/column_length.twig", "/var/www/html/secure/phpMyAdmin/templates/columns_definitions/column_length.twig");
    }
}

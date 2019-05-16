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

/* columns_definitions/column_default.twig */
class __TwigTemplate_0083f321aae015c10fa1667a4b4d1cdb8dd3e859620261ad0ed6bfade77c213a extends \Twig\Template
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
        // line 3
        ob_start();
        echo _pgettext(        "for default", "None");
        $context["translation"] = ('' === $tmp = ob_get_clean()) ? '' : new Markup($tmp, $this->env->getCharset());
        // line 4
        $context["default_options"] = ["NONE" =>         // line 5
($context["translation"] ?? null), "USER_DEFINED" => _gettext("As defined:"), "NULL" => "NULL", "CURRENT_TIMESTAMP" => "CURRENT_TIMESTAMP"];
        // line 10
        echo "
<select name=\"field_default_type[";
        // line 11
        echo twig_escape_filter($this->env, ($context["column_number"] ?? null), "html", null, true);
        echo "]\"
    id=\"field_";
        // line 12
        echo twig_escape_filter($this->env, ($context["column_number"] ?? null), "html", null, true);
        echo "_";
        echo twig_escape_filter($this->env, (($context["ci"] ?? null) - ($context["ci_offset"] ?? null)), "html", null, true);
        echo "\"
    class=\"default_type\">
    ";
        // line 14
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["default_options"] ?? null));
        foreach ($context['_seq'] as $context["key"] => $context["value"]) {
            // line 15
            echo "        <option value=\"";
            echo twig_escape_filter($this->env, $context["key"], "html", null, true);
            echo "\"";
            // line 16
            if ((twig_get_attribute($this->env, $this->source, ($context["column_meta"] ?? null), "DefaultType", [], "array", true, true, false, 16) && ((($__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4 =             // line 17
($context["column_meta"] ?? null)) && is_array($__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4) || $__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4 instanceof ArrayAccess ? ($__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4["DefaultType"] ?? null) : null) == $context["key"]))) {
                // line 18
                echo "                selected=\"selected\"";
            }
            // line 19
            echo ">
            ";
            // line 20
            echo twig_escape_filter($this->env, $context["value"], "html", null, true);
            echo "
        </option>
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['key'], $context['value'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 23
        echo "</select>
";
        // line 24
        if ((($context["char_editing"] ?? null) == "textarea")) {
            // line 25
            echo "    <textarea name=\"field_default_value[";
            echo twig_escape_filter($this->env, ($context["column_number"] ?? null), "html", null, true);
            echo "]\"
        cols=\"15\"
        class=\"textfield
        default_value\">";
            // line 28
            echo twig_escape_filter($this->env, ($context["default_value"] ?? null), "html", null, true);
            echo "</textarea>
";
        } else {
            // line 30
            echo "    <input type=\"text\"
        name=\"field_default_value[";
            // line 31
            echo twig_escape_filter($this->env, ($context["column_number"] ?? null), "html", null, true);
            echo "]\"
        size=\"12\"
        value=\"";
            // line 33
            echo twig_escape_filter($this->env, ($context["default_value"] ?? null), "html", null, true);
            echo "\"
        class=\"textfield default_value\">
";
        }
    }

    public function getTemplateName()
    {
        return "columns_definitions/column_default.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  107 => 33,  102 => 31,  99 => 30,  94 => 28,  87 => 25,  85 => 24,  82 => 23,  73 => 20,  70 => 19,  67 => 18,  65 => 17,  64 => 16,  60 => 15,  56 => 14,  49 => 12,  45 => 11,  42 => 10,  40 => 5,  39 => 4,  35 => 3,);
    }

    public function getSourceContext()
    {
        return new Source("", "columns_definitions/column_default.twig", "/var/www/html/secure/phpMyAdmin/templates/columns_definitions/column_default.twig");
    }
}

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

/* display/export/options_quick_export.twig */
class __TwigTemplate_1b488765d70eb6761e7a791a98d61a7aa83b4030fe2981ea0159955e411386b1 extends \Twig\Template
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
        echo "<div class=\"exportoptions\" id=\"output_quick_export\">
    <h3>";
        // line 2
        echo _gettext("Output:");
        echo "</h3>
    <ul>
        <li>
            <input type=\"checkbox\" name=\"quick_export_onserver\" value=\"saveit\"
                id=\"checkbox_quick_dump_onserver\"";
        // line 6
        echo ((($context["export_is_checked"] ?? null)) ? (" checked") : (""));
        echo ">
            <label for=\"checkbox_quick_dump_onserver\">
                ";
        // line 8
        echo sprintf(_gettext("Save on server in the directory <strong>%s</strong>"), twig_escape_filter($this->env, ($context["save_dir"] ?? null)));
        echo "
            </label>
        </li>
        <li>
            <input type=\"checkbox\" name=\"quick_export_onserver_overwrite\"
                value=\"saveitover\" id=\"checkbox_quick_dump_onserver_overwrite\"";
        // line 14
        echo ((($context["export_overwrite_is_checked"] ?? null)) ? (" checked") : (""));
        echo ">
            <label for=\"checkbox_quick_dump_onserver_overwrite\">
                ";
        // line 16
        echo _gettext("Overwrite existing file(s)");
        // line 17
        echo "            </label>
        </li>
    </ul>
</div>
";
    }

    public function getTemplateName()
    {
        return "display/export/options_quick_export.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  65 => 17,  63 => 16,  58 => 14,  50 => 8,  45 => 6,  38 => 2,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "display/export/options_quick_export.twig", "/home/files/phpmyadmin/release/phpMyAdmin-5.0+snapshot/templates/display/export/options_quick_export.twig");
    }
}

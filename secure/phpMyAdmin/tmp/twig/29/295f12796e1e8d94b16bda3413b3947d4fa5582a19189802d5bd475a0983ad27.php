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

/* display/export/options_output_compression.twig */
class __TwigTemplate_69773a4a217bd75534af42cbbdcb988a543cbef67f0a77e16ac012b80d057748 extends \Twig\Template
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
        if ((($context["is_zip"] ?? null) || ($context["is_gzip"] ?? null))) {
            // line 2
            echo "    <li>
        <label for=\"compression\" class=\"desc\">
            ";
            // line 4
            echo _gettext("Compression:");
            // line 5
            echo "        </label>
        <select id=\"compression\" name=\"compression\">
            <option value=\"none\">";
            // line 7
            echo _gettext("None");
            echo "</option>
            ";
            // line 8
            if (($context["is_zip"] ?? null)) {
                // line 9
                echo "                <option value=\"zip\"";
                // line 10
                echo (((($context["selected_compression"] ?? null) == "zip")) ? (" selected") : (""));
                echo ">
                    ";
                // line 11
                echo _gettext("zipped");
                // line 12
                echo "                </option>
            ";
            }
            // line 14
            echo "            ";
            if (($context["is_gzip"] ?? null)) {
                // line 15
                echo "                <option value=\"gzip\"";
                // line 16
                echo (((($context["selected_compression"] ?? null) == "gzip")) ? (" selected") : (""));
                echo ">
                    ";
                // line 17
                echo _gettext("gzipped");
                // line 18
                echo "                </option>
            ";
            }
            // line 20
            echo "        </select>
    </li>
";
        } else {
            // line 23
            echo "    <input type=\"hidden\" name=\"compression\" value=\"";
            echo twig_escape_filter($this->env, ($context["selected_compression"] ?? null), "html", null, true);
            echo "\">
";
        }
    }

    public function getTemplateName()
    {
        return "display/export/options_output_compression.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  85 => 23,  80 => 20,  76 => 18,  74 => 17,  70 => 16,  68 => 15,  65 => 14,  61 => 12,  59 => 11,  55 => 10,  53 => 9,  51 => 8,  47 => 7,  43 => 5,  41 => 4,  37 => 2,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "display/export/options_output_compression.twig", "/var/www/html/secure/phpMyAdmin/templates/display/export/options_output_compression.twig");
    }
}

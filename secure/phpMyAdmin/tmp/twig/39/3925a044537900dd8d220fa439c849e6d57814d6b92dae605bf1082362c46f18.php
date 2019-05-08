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

/* display/results/options_block.twig */
class __TwigTemplate_9664d76bca471fdd2dfc54f6d8308c639be429702b0b8c3b69dcea2710f424f0 extends \Twig\Template
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
        echo "<form method=\"post\" action=\"sql.php\" name=\"displayOptionsForm\" class=\"ajax print_ignore\">
    ";
        // line 2
        echo PhpMyAdmin\Url::getHiddenInputs(["db" =>         // line 3
($context["db"] ?? null), "table" =>         // line 4
($context["table"] ?? null), "sql_query" =>         // line 5
($context["sql_query"] ?? null), "goto" =>         // line 6
($context["goto"] ?? null), "display_options_form" => 1]);
        // line 8
        echo "

    ";
        // line 10
        $this->loadTemplate("div_for_slider_effect.twig", "display/results/options_block.twig", 10)->display(twig_to_array(["id" => "", "message" => _gettext("Options"), "initial_sliders_state" =>         // line 13
($context["default_sliders_state"] ?? null)]));
        // line 15
        echo "        <fieldset>
            <div class=\"formelement\">
                ";
        // line 18
        echo "                ";
        echo PhpMyAdmin\Util::getRadioFields("pftext", ["P" => _gettext("Partial texts"), "F" => _gettext("Full texts")],         // line 24
($context["pftext"] ?? null), true, true, "", ("pftext_" .         // line 28
($context["unique_id"] ?? null)));
        // line 29
        echo "
            </div>

            ";
        // line 32
        if ((($context["relwork"] ?? null) && ($context["displaywork"] ?? null))) {
            // line 33
            echo "                <div class=\"formelement\">
                    ";
            // line 34
            echo PhpMyAdmin\Util::getRadioFields("relational_display", ["K" => _gettext("Relational key"), "D" => _gettext("Display column for relationships")],             // line 40
($context["relational_display"] ?? null), true, true, "", ("relational_display_" .             // line 44
($context["unique_id"] ?? null)));
            // line 45
            echo "
                </div>
            ";
        }
        // line 48
        echo "
            <div class=\"formelement\">
                ";
        // line 50
        $this->loadTemplate("checkbox.twig", "display/results/options_block.twig", 50)->display(twig_to_array(["html_field_name" => "display_binary", "label" => _gettext("Show binary contents"), "checked" =>  !twig_test_empty(        // line 53
($context["display_binary"] ?? null)), "onclick" => false, "html_field_id" => ("display_binary_" .         // line 55
($context["unique_id"] ?? null))]));
        // line 57
        echo "                ";
        $this->loadTemplate("checkbox.twig", "display/results/options_block.twig", 57)->display(twig_to_array(["html_field_name" => "display_blob", "label" => _gettext("Show BLOB contents"), "checked" =>  !twig_test_empty(        // line 60
($context["display_blob"] ?? null)), "onclick" => false, "html_field_id" => ("display_blob_" .         // line 62
($context["unique_id"] ?? null))]));
        // line 64
        echo "            </div>

            ";
        // line 70
        echo "            <div class=\"formelement\">
                ";
        // line 71
        $this->loadTemplate("checkbox.twig", "display/results/options_block.twig", 71)->display(twig_to_array(["html_field_name" => "hide_transformation", "label" => _gettext("Hide browser transformation"), "checked" =>  !twig_test_empty(        // line 74
($context["hide_transformation"] ?? null)), "onclick" => false, "html_field_id" => ("hide_transformation_" .         // line 76
($context["unique_id"] ?? null))]));
        // line 78
        echo "            </div>


            ";
        // line 81
        if (($context["possible_as_geometry"] ?? null)) {
            // line 82
            echo "                <div class=\"formelement\">
                    ";
            // line 83
            echo PhpMyAdmin\Util::getRadioFields("geoOption", ["GEOM" => _gettext("Geometry"), "WKT" => _gettext("Well Known Text"), "WKB" => _gettext("Well Known Binary")],             // line 90
($context["geo_option"] ?? null), true, true, "", ("geoOption_" .             // line 94
($context["unique_id"] ?? null)));
            // line 95
            echo "
                </div>
            ";
        } else {
            // line 98
            echo "                <div class=\"formelement\">
                    ";
            // line 99
            echo twig_escape_filter($this->env, ($context["possible_as_geometry"] ?? null), "html", null, true);
            echo "
                    ";
            // line 100
            echo PhpMyAdmin\Util::getRadioFields("geoOption", ["WKT" => _gettext("Well Known Text"), "WKB" => _gettext("Well Known Binary")],             // line 106
($context["geo_option"] ?? null), true, true, "", ("geoOption_" .             // line 110
($context["unique_id"] ?? null)));
            // line 111
            echo "
                </div>
            ";
        }
        // line 114
        echo "            <div class=\"clearfloat\"></div>
        </fieldset>

        <fieldset class=\"tblFooters\">
            <input class=\"btn btn-primary\" type=\"submit\" value=\"";
        // line 118
        echo _gettext("Go");
        echo "\">
        </fieldset>
    </div>";
        // line 121
        echo "</form>
";
    }

    public function getTemplateName()
    {
        return "display/results/options_block.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  149 => 121,  144 => 118,  138 => 114,  133 => 111,  131 => 110,  130 => 106,  129 => 100,  125 => 99,  122 => 98,  117 => 95,  115 => 94,  114 => 90,  113 => 83,  110 => 82,  108 => 81,  103 => 78,  101 => 76,  100 => 74,  99 => 71,  96 => 70,  92 => 64,  90 => 62,  89 => 60,  87 => 57,  85 => 55,  84 => 53,  83 => 50,  79 => 48,  74 => 45,  72 => 44,  71 => 40,  70 => 34,  67 => 33,  65 => 32,  60 => 29,  58 => 28,  57 => 24,  55 => 18,  51 => 15,  49 => 13,  48 => 10,  44 => 8,  42 => 6,  41 => 5,  40 => 4,  39 => 3,  38 => 2,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "display/results/options_block.twig", "/var/www/html/secure/phpMyAdmin/templates/display/results/options_block.twig");
    }
}

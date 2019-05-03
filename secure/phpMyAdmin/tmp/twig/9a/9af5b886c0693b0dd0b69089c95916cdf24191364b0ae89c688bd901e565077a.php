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

/* export/alias_add.twig */
class __TwigTemplate_1c71d1acfe543eb77c2b9ee706f94fa9177718f61bc037640c411cefbc7b5c79 extends \Twig\Template
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
        echo "<table>
    <thead>
        <tr>
            <th colspan=\"4\">";
        // line 4
        echo _gettext("Define new aliases");
        echo "</th>
        </tr>
    </thead>
    <tr>
        <td>
            <label>";
        // line 9
        echo _gettext("Select database:");
        echo "</label>
        </td>
        <td>
            <select id=\"db_alias_select\"><option value=\"\"></option></select>
        </td>
        <td>
            <input id=\"db_alias_name\" placeholder=\"";
        // line 15
        echo _gettext("New database name");
        echo "\" disabled=\"1\">
        </td>
        <td>
            <button id=\"db_alias_button\" class=\"ui-button ui-corner-all ui-widget\" disabled=\"1\">";
        // line 18
        echo _gettext("Add");
        echo "</button>
        </td>
    </tr>
    <tr>
        <td>
            <label>";
        // line 23
        echo _gettext("Select table:");
        echo "</label>
        </td>
        <td>
            <select id=\"table_alias_select\"><option value=\"\"></option></select>
        </td>
        <td>
            <input id=\"table_alias_name\" placeholder=\"";
        // line 29
        echo _gettext("New table name");
        echo "\" disabled=\"1\">
        </td>
        <td>
            <button id=\"table_alias_button\" class=\"ui-button ui-corner-all ui-widget\" disabled=\"1\">";
        // line 32
        echo _gettext("Add");
        echo "</button>
        </td>
    </tr>
    <tr>
        <td>
            <label>";
        // line 37
        echo _gettext("Select column:");
        echo "</label>
        </td>
        <td>
            <select id=\"column_alias_select\"><option value=\"\"></option></select>
        </td>
        <td>
            <input id=\"column_alias_name\" placeholder=\"";
        // line 43
        echo _gettext("New column name");
        echo "\" disabled=\"1\">
        </td>
        <td>
            <button id=\"column_alias_button\" class=\"ui-button ui-corner-all ui-widget\" disabled=\"1\">";
        // line 46
        echo _gettext("Add");
        echo "</button>
        </td>
    </tr>
</table>
";
    }

    public function getTemplateName()
    {
        return "export/alias_add.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  109 => 46,  103 => 43,  94 => 37,  86 => 32,  80 => 29,  71 => 23,  63 => 18,  57 => 15,  48 => 9,  40 => 4,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "export/alias_add.twig", "/home/files/phpmyadmin/release/phpMyAdmin-5.0+snapshot/templates/export/alias_add.twig");
    }
}

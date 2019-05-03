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

/* table/tracking/structure_snapshot_columns.twig */
class __TwigTemplate_04e1f20eb3680ba0dff358e9bb8ca9f4b93158add878fd6ed9bf498a9c7ccdd0 extends \Twig\Template
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
        echo "<h3>";
        echo _gettext("Structure");
        echo "</h3>
<table id=\"tablestructure\" class=\"data\">
    <thead>
        <tr>
            <th>";
        // line 5
        echo _pgettext(        "Number", "#");
        echo "</th>
            <th>";
        // line 6
        echo _gettext("Column");
        echo "</th>
            <th>";
        // line 7
        echo _gettext("Type");
        echo "</th>
            <th>";
        // line 8
        echo _gettext("Collation");
        echo "</th>
            <th>";
        // line 9
        echo _gettext("Null");
        echo "</th>
            <th>";
        // line 10
        echo _gettext("Default");
        echo "</th>
            <th>";
        // line 11
        echo _gettext("Extra");
        echo "</th>
            <th>";
        // line 12
        echo _gettext("Comment");
        echo "</th>
        </tr>
    </thead>
    <tbody>
        ";
        // line 16
        $context["index"] = 1;
        // line 17
        echo "        ";
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["columns"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["field"]) {
            // line 18
            echo "            <tr class=\"noclick\">
                <td>";
            // line 19
            echo twig_escape_filter($this->env, ($context["index"] ?? null), "html", null, true);
            echo "</td>
                ";
            // line 20
            $context["index"] = (($context["index"] ?? null) + 1);
            // line 21
            echo "                <td>
                    <strong>
                        ";
            // line 23
            echo twig_escape_filter($this->env, (($__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4 = $context["field"]) && is_array($__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4) || $__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4 instanceof ArrayAccess ? ($__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4["Field"] ?? null) : null), "html", null, true);
            echo "
                        ";
            // line 24
            if (((($__internal_62824350bc4502ee19dbc2e99fc6bdd3bd90e7d8dd6e72f42c35efd048542144 = $context["field"]) && is_array($__internal_62824350bc4502ee19dbc2e99fc6bdd3bd90e7d8dd6e72f42c35efd048542144) || $__internal_62824350bc4502ee19dbc2e99fc6bdd3bd90e7d8dd6e72f42c35efd048542144 instanceof ArrayAccess ? ($__internal_62824350bc4502ee19dbc2e99fc6bdd3bd90e7d8dd6e72f42c35efd048542144["Key"] ?? null) : null) == "PRI")) {
                // line 25
                echo "                            ";
                echo PhpMyAdmin\Util::getImage("b_primary", _gettext("Primary"));
                echo "
                        ";
            } elseif ( !twig_test_empty((($__internal_1cfccaec8dd2e8578ccb026fbe7f2e7e29ac2ed5deb976639c5fc99a6ea8583b =             // line 26
$context["field"]) && is_array($__internal_1cfccaec8dd2e8578ccb026fbe7f2e7e29ac2ed5deb976639c5fc99a6ea8583b) || $__internal_1cfccaec8dd2e8578ccb026fbe7f2e7e29ac2ed5deb976639c5fc99a6ea8583b instanceof ArrayAccess ? ($__internal_1cfccaec8dd2e8578ccb026fbe7f2e7e29ac2ed5deb976639c5fc99a6ea8583b["Key"] ?? null) : null))) {
                // line 27
                echo "                            ";
                echo PhpMyAdmin\Util::getImage("bd_primary", _gettext("Index"));
                echo "
                        ";
            }
            // line 29
            echo "                    </strong>
                </td>
                <td>";
            // line 31
            echo twig_escape_filter($this->env, (($__internal_68aa442c1d43d3410ea8f958ba9090f3eaa9a76f8de8fc9be4d6c7389ba28002 = $context["field"]) && is_array($__internal_68aa442c1d43d3410ea8f958ba9090f3eaa9a76f8de8fc9be4d6c7389ba28002) || $__internal_68aa442c1d43d3410ea8f958ba9090f3eaa9a76f8de8fc9be4d6c7389ba28002 instanceof ArrayAccess ? ($__internal_68aa442c1d43d3410ea8f958ba9090f3eaa9a76f8de8fc9be4d6c7389ba28002["Type"] ?? null) : null), "html", null, true);
            echo "</td>
                <td>";
            // line 32
            echo twig_escape_filter($this->env, (($__internal_d7fc55f1a54b629533d60b43063289db62e68921ee7a5f8de562bd9d4a2b7ad4 = $context["field"]) && is_array($__internal_d7fc55f1a54b629533d60b43063289db62e68921ee7a5f8de562bd9d4a2b7ad4) || $__internal_d7fc55f1a54b629533d60b43063289db62e68921ee7a5f8de562bd9d4a2b7ad4 instanceof ArrayAccess ? ($__internal_d7fc55f1a54b629533d60b43063289db62e68921ee7a5f8de562bd9d4a2b7ad4["Collation"] ?? null) : null), "html", null, true);
            echo "</td>
                <td>";
            // line 33
            echo twig_escape_filter($this->env, ((((($__internal_01476f8db28655ee4ee02ea2d17dd5a92599be76304f08cd8bc0e05aced30666 = $context["field"]) && is_array($__internal_01476f8db28655ee4ee02ea2d17dd5a92599be76304f08cd8bc0e05aced30666) || $__internal_01476f8db28655ee4ee02ea2d17dd5a92599be76304f08cd8bc0e05aced30666 instanceof ArrayAccess ? ($__internal_01476f8db28655ee4ee02ea2d17dd5a92599be76304f08cd8bc0e05aced30666["Null"] ?? null) : null) == "YES")) ? (_gettext("Yes")) : (_gettext("No"))), "html", null, true);
            echo "</td>
                <td>
                    ";
            // line 35
            if (twig_get_attribute($this->env, $this->source, $context["field"], "Default", [], "array", true, true, false, 35)) {
                // line 36
                echo "                        ";
                $context["extracted_columnspec"] = PhpMyAdmin\Util::extractColumnSpec((($__internal_01c35b74bd85735098add188b3f8372ba465b232ab8298cb582c60f493d3c22e = $context["field"]) && is_array($__internal_01c35b74bd85735098add188b3f8372ba465b232ab8298cb582c60f493d3c22e) || $__internal_01c35b74bd85735098add188b3f8372ba465b232ab8298cb582c60f493d3c22e instanceof ArrayAccess ? ($__internal_01c35b74bd85735098add188b3f8372ba465b232ab8298cb582c60f493d3c22e["Type"] ?? null) : null));
                // line 37
                echo "                        ";
                if (((($__internal_63ad1f9a2bf4db4af64b010785e9665558fdcac0e8db8b5b413ed986c62dbb52 = ($context["extracted_columnspec"] ?? null)) && is_array($__internal_63ad1f9a2bf4db4af64b010785e9665558fdcac0e8db8b5b413ed986c62dbb52) || $__internal_63ad1f9a2bf4db4af64b010785e9665558fdcac0e8db8b5b413ed986c62dbb52 instanceof ArrayAccess ? ($__internal_63ad1f9a2bf4db4af64b010785e9665558fdcac0e8db8b5b413ed986c62dbb52["type"] ?? null) : null) == "bit")) {
                    // line 38
                    echo "                            ";
                    // line 39
                    echo "                            ";
                    echo twig_escape_filter($this->env, PhpMyAdmin\Util::convertBitDefaultValue((($__internal_f10a4cc339617934220127f034125576ed229e948660ebac906a15846d52f136 = $context["field"]) && is_array($__internal_f10a4cc339617934220127f034125576ed229e948660ebac906a15846d52f136) || $__internal_f10a4cc339617934220127f034125576ed229e948660ebac906a15846d52f136 instanceof ArrayAccess ? ($__internal_f10a4cc339617934220127f034125576ed229e948660ebac906a15846d52f136["Default"] ?? null) : null)), "html", null, true);
                    echo "
                        ";
                } else {
                    // line 41
                    echo "                            ";
                    echo twig_escape_filter($this->env, (($__internal_887a873a4dc3cf8bd4f99c487b4c7727999c350cc3a772414714e49a195e4386 = $context["field"]) && is_array($__internal_887a873a4dc3cf8bd4f99c487b4c7727999c350cc3a772414714e49a195e4386) || $__internal_887a873a4dc3cf8bd4f99c487b4c7727999c350cc3a772414714e49a195e4386 instanceof ArrayAccess ? ($__internal_887a873a4dc3cf8bd4f99c487b4c7727999c350cc3a772414714e49a195e4386["Default"] ?? null) : null), "html", null, true);
                    echo "
                        ";
                }
                // line 43
                echo "                    ";
            } else {
                // line 44
                echo "                        ";
                if (((($__internal_d527c24a729d38501d770b40a0d25e1ce8a7f0bff897cc4f8f449ba71fcff3d9 = $context["field"]) && is_array($__internal_d527c24a729d38501d770b40a0d25e1ce8a7f0bff897cc4f8f449ba71fcff3d9) || $__internal_d527c24a729d38501d770b40a0d25e1ce8a7f0bff897cc4f8f449ba71fcff3d9 instanceof ArrayAccess ? ($__internal_d527c24a729d38501d770b40a0d25e1ce8a7f0bff897cc4f8f449ba71fcff3d9["Null"] ?? null) : null) == "YES")) {
                    // line 45
                    echo "                            <em>NULL</em>
                        ";
                } else {
                    // line 47
                    echo "                            <em>";
                    echo _pgettext(                    "None for default", "None");
                    echo "</em>
                        ";
                }
                // line 49
                echo "                    ";
            }
            // line 50
            echo "                </td>
                <td>";
            // line 51
            echo twig_escape_filter($this->env, (($__internal_f6dde3a1020453fdf35e718e94f93ce8eb8803b28cc77a665308e14bbe8572ae = $context["field"]) && is_array($__internal_f6dde3a1020453fdf35e718e94f93ce8eb8803b28cc77a665308e14bbe8572ae) || $__internal_f6dde3a1020453fdf35e718e94f93ce8eb8803b28cc77a665308e14bbe8572ae instanceof ArrayAccess ? ($__internal_f6dde3a1020453fdf35e718e94f93ce8eb8803b28cc77a665308e14bbe8572ae["Extra"] ?? null) : null), "html", null, true);
            echo "</td>
                <td>";
            // line 52
            echo twig_escape_filter($this->env, (($__internal_25c0fab8152b8dd6b90603159c0f2e8a936a09ab76edb5e4d7bc95d9a8d2dc8f = $context["field"]) && is_array($__internal_25c0fab8152b8dd6b90603159c0f2e8a936a09ab76edb5e4d7bc95d9a8d2dc8f) || $__internal_25c0fab8152b8dd6b90603159c0f2e8a936a09ab76edb5e4d7bc95d9a8d2dc8f instanceof ArrayAccess ? ($__internal_25c0fab8152b8dd6b90603159c0f2e8a936a09ab76edb5e4d7bc95d9a8d2dc8f["Comment"] ?? null) : null), "html", null, true);
            echo "</td>
            </tr>
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['field'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 55
        echo "    </tbody>
</table>
";
    }

    public function getTemplateName()
    {
        return "table/tracking/structure_snapshot_columns.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  191 => 55,  182 => 52,  178 => 51,  175 => 50,  172 => 49,  166 => 47,  162 => 45,  159 => 44,  156 => 43,  150 => 41,  144 => 39,  142 => 38,  139 => 37,  136 => 36,  134 => 35,  129 => 33,  125 => 32,  121 => 31,  117 => 29,  111 => 27,  109 => 26,  104 => 25,  102 => 24,  98 => 23,  94 => 21,  92 => 20,  88 => 19,  85 => 18,  80 => 17,  78 => 16,  71 => 12,  67 => 11,  63 => 10,  59 => 9,  55 => 8,  51 => 7,  47 => 6,  43 => 5,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "table/tracking/structure_snapshot_columns.twig", "/home/files/phpmyadmin/release/phpMyAdmin-5.0+snapshot/templates/table/tracking/structure_snapshot_columns.twig");
    }
}

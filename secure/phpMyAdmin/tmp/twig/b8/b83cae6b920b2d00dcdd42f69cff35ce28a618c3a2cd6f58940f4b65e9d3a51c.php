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

/* server/binlog/index.twig */
class __TwigTemplate_cebe2f5904eda924c45dd4b62628dd9f09e39136acdf9e9267cfa55bfa49b3df extends \Twig\Template
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
        echo "<h2>
  ";
        // line 2
        echo PhpMyAdmin\Util::getImage("s_tbl");
        echo "
  ";
        // line 3
        echo _gettext("Binary log");
        // line 4
        echo "</h2>

<form action=\"server_binlog.php\" method=\"post\">
  ";
        // line 7
        echo PhpMyAdmin\Url::getHiddenInputs(($context["url_params"] ?? null));
        echo "
  <fieldset>
    <legend>
      ";
        // line 10
        echo _gettext("Select binary log to view");
        // line 11
        echo "    </legend>

    ";
        // line 13
        $context["full_size"] = 0;
        // line 14
        echo "    <select name=\"log\">
      ";
        // line 15
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["binary_logs"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["each_log"]) {
            // line 16
            echo "        <option value=\"";
            echo twig_escape_filter($this->env, (($__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4 = $context["each_log"]) && is_array($__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4) || $__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4 instanceof ArrayAccess ? ($__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4["Log_name"] ?? null) : null), "html", null, true);
            echo "\"";
            // line 17
            echo ((((($__internal_62824350bc4502ee19dbc2e99fc6bdd3bd90e7d8dd6e72f42c35efd048542144 = $context["each_log"]) && is_array($__internal_62824350bc4502ee19dbc2e99fc6bdd3bd90e7d8dd6e72f42c35efd048542144) || $__internal_62824350bc4502ee19dbc2e99fc6bdd3bd90e7d8dd6e72f42c35efd048542144 instanceof ArrayAccess ? ($__internal_62824350bc4502ee19dbc2e99fc6bdd3bd90e7d8dd6e72f42c35efd048542144["Log_name"] ?? null) : null) == ($context["log"] ?? null))) ? (" selected") : (""));
            echo ">
          ";
            // line 18
            echo twig_escape_filter($this->env, (($__internal_1cfccaec8dd2e8578ccb026fbe7f2e7e29ac2ed5deb976639c5fc99a6ea8583b = $context["each_log"]) && is_array($__internal_1cfccaec8dd2e8578ccb026fbe7f2e7e29ac2ed5deb976639c5fc99a6ea8583b) || $__internal_1cfccaec8dd2e8578ccb026fbe7f2e7e29ac2ed5deb976639c5fc99a6ea8583b instanceof ArrayAccess ? ($__internal_1cfccaec8dd2e8578ccb026fbe7f2e7e29ac2ed5deb976639c5fc99a6ea8583b["Log_name"] ?? null) : null), "html", null, true);
            echo "
          ";
            // line 19
            if (twig_get_attribute($this->env, $this->source, $context["each_log"], "File_size", [], "array", true, true, false, 19)) {
                // line 20
                echo "            (";
                echo twig_escape_filter($this->env, twig_join_filter(PhpMyAdmin\Util::formatByteDown((($__internal_68aa442c1d43d3410ea8f958ba9090f3eaa9a76f8de8fc9be4d6c7389ba28002 = $context["each_log"]) && is_array($__internal_68aa442c1d43d3410ea8f958ba9090f3eaa9a76f8de8fc9be4d6c7389ba28002) || $__internal_68aa442c1d43d3410ea8f958ba9090f3eaa9a76f8de8fc9be4d6c7389ba28002 instanceof ArrayAccess ? ($__internal_68aa442c1d43d3410ea8f958ba9090f3eaa9a76f8de8fc9be4d6c7389ba28002["File_size"] ?? null) : null), 3, 2), " "), "html", null, true);
                echo ")
            ";
                // line 21
                $context["full_size"] = (($context["full_size"] ?? null) + (($__internal_d7fc55f1a54b629533d60b43063289db62e68921ee7a5f8de562bd9d4a2b7ad4 = $context["each_log"]) && is_array($__internal_d7fc55f1a54b629533d60b43063289db62e68921ee7a5f8de562bd9d4a2b7ad4) || $__internal_d7fc55f1a54b629533d60b43063289db62e68921ee7a5f8de562bd9d4a2b7ad4 instanceof ArrayAccess ? ($__internal_d7fc55f1a54b629533d60b43063289db62e68921ee7a5f8de562bd9d4a2b7ad4["File_size"] ?? null) : null));
                // line 22
                echo "          ";
            }
            // line 23
            echo "        </option>
      ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['each_log'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 25
        echo "    </select>
    ";
        // line 26
        echo twig_escape_filter($this->env, twig_length_filter($this->env, ($context["binary_logs"] ?? null)), "html", null, true);
        echo "
    ";
        // line 27
        echo _gettext("Files");
        echo ",
    ";
        // line 28
        if ((($context["full_size"] ?? null) > 0)) {
            // line 29
            echo "      ";
            echo twig_escape_filter($this->env, twig_join_filter(PhpMyAdmin\Util::formatByteDown(($context["full_size"] ?? null)), " "), "html", null, true);
            echo "
    ";
        }
        // line 31
        echo "  </fieldset>

  <fieldset class=\"tblFooters\">
    <input class=\"btn btn-primary\" type=\"submit\" value=\"";
        // line 34
        echo _gettext("Go");
        echo "\">
  </fieldset>
</form>

";
        // line 38
        echo ($context["sql_message"] ?? null);
        echo "

<table id=\"binlogTable\">
  <thead>
    <tr>
      <td colspan=\"6\" class=\"center\">
        ";
        // line 44
        if (($context["has_previous"] ?? null)) {
            // line 45
            echo "          ";
            if (($context["has_icons"] ?? null)) {
                // line 46
                echo "            <a href=\"server_binlog.php\" data-post=\"";
                echo PhpMyAdmin\Url::getCommon(($context["previous_params"] ?? null), "");
                echo "\" title=\"";
                // line 47
                echo _pgettext(                "Previous page", "Previous");
                echo "\">
              &laquo;
            </a>
          ";
            } else {
                // line 51
                echo "            <a href=\"server_binlog.php\" data-post=\"";
                echo PhpMyAdmin\Url::getCommon(($context["previous_params"] ?? null), "");
                echo "\">
              ";
                // line 52
                echo _pgettext(                "Previous page", "Previous");
                echo " &laquo;
            </a>
          ";
            }
            // line 55
            echo "          -
        ";
        }
        // line 57
        echo "
        ";
        // line 58
        if (($context["is_full_query"] ?? null)) {
            // line 59
            echo "          <a href=\"server_binlog.php\" data-post=\"";
            echo PhpMyAdmin\Url::getCommon(($context["full_queries_params"] ?? null), "");
            echo "\" title=\"";
            echo _gettext("Truncate shown queries");
            echo "\">
            <img src=\"";
            // line 60
            echo twig_escape_filter($this->env, ($context["image_path"] ?? null), "html", null, true);
            echo "s_partialtext.png\" alt=\"";
            echo _gettext("Truncate shown queries");
            echo "\">
          </a>
        ";
        } else {
            // line 63
            echo "          <a href=\"server_binlog.php\" data-post=\"";
            echo PhpMyAdmin\Url::getCommon(($context["full_queries_params"] ?? null), "");
            echo "\" title=\"";
            echo _gettext("Show full queries");
            echo "\">
            <img src=\"";
            // line 64
            echo twig_escape_filter($this->env, ($context["image_path"] ?? null), "html", null, true);
            echo "s_fulltext.png\" alt=\"";
            echo _gettext("Show full queries");
            echo "\">
          </a>
        ";
        }
        // line 67
        echo "
        ";
        // line 68
        if (($context["has_next"] ?? null)) {
            // line 69
            echo "          -
          ";
            // line 70
            if (($context["has_icons"] ?? null)) {
                // line 71
                echo "            <a href=\"server_binlog.php\" data-post=\"";
                echo PhpMyAdmin\Url::getCommon(($context["next_params"] ?? null), "");
                echo "\" title=\"";
                // line 72
                echo _pgettext(                "Next page", "Next");
                echo "\">
              &raquo;
            </a>
          ";
            } else {
                // line 76
                echo "            <a href=\"server_binlog.php\" data-post=\"";
                echo PhpMyAdmin\Url::getCommon(($context["next_params"] ?? null), "");
                echo "\">
              ";
                // line 77
                echo _pgettext(                "Next page", "Next");
                echo " &raquo;
            </a>
          ";
            }
            // line 80
            echo "        ";
        }
        // line 81
        echo "      </td>
    </tr>
    <tr>
      <th>";
        // line 84
        echo _gettext("Log name");
        echo "</th>
      <th>";
        // line 85
        echo _gettext("Position");
        echo "</th>
      <th>";
        // line 86
        echo _gettext("Event type");
        echo "</th>
      <th>";
        // line 87
        echo _gettext("Server ID");
        echo "</th>
      <th>";
        // line 88
        echo _gettext("Original position");
        echo "</th>
      <th>";
        // line 89
        echo _gettext("Information");
        echo "</th>
    </tr>
  </thead>

  <tbody>
    ";
        // line 94
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["values"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["value"]) {
            // line 95
            echo "      <tr class=\"noclick\">
        <td>";
            // line 96
            echo twig_escape_filter($this->env, (($__internal_01476f8db28655ee4ee02ea2d17dd5a92599be76304f08cd8bc0e05aced30666 = $context["value"]) && is_array($__internal_01476f8db28655ee4ee02ea2d17dd5a92599be76304f08cd8bc0e05aced30666) || $__internal_01476f8db28655ee4ee02ea2d17dd5a92599be76304f08cd8bc0e05aced30666 instanceof ArrayAccess ? ($__internal_01476f8db28655ee4ee02ea2d17dd5a92599be76304f08cd8bc0e05aced30666["Log_name"] ?? null) : null), "html", null, true);
            echo "</td>
        <td class=\"right\">";
            // line 97
            echo twig_escape_filter($this->env, (($__internal_01c35b74bd85735098add188b3f8372ba465b232ab8298cb582c60f493d3c22e = $context["value"]) && is_array($__internal_01c35b74bd85735098add188b3f8372ba465b232ab8298cb582c60f493d3c22e) || $__internal_01c35b74bd85735098add188b3f8372ba465b232ab8298cb582c60f493d3c22e instanceof ArrayAccess ? ($__internal_01c35b74bd85735098add188b3f8372ba465b232ab8298cb582c60f493d3c22e["Pos"] ?? null) : null), "html", null, true);
            echo "</td>
        <td>";
            // line 98
            echo twig_escape_filter($this->env, (($__internal_63ad1f9a2bf4db4af64b010785e9665558fdcac0e8db8b5b413ed986c62dbb52 = $context["value"]) && is_array($__internal_63ad1f9a2bf4db4af64b010785e9665558fdcac0e8db8b5b413ed986c62dbb52) || $__internal_63ad1f9a2bf4db4af64b010785e9665558fdcac0e8db8b5b413ed986c62dbb52 instanceof ArrayAccess ? ($__internal_63ad1f9a2bf4db4af64b010785e9665558fdcac0e8db8b5b413ed986c62dbb52["Event_type"] ?? null) : null), "html", null, true);
            echo "</td>
        <td class=\"right\">";
            // line 99
            echo twig_escape_filter($this->env, (($__internal_f10a4cc339617934220127f034125576ed229e948660ebac906a15846d52f136 = $context["value"]) && is_array($__internal_f10a4cc339617934220127f034125576ed229e948660ebac906a15846d52f136) || $__internal_f10a4cc339617934220127f034125576ed229e948660ebac906a15846d52f136 instanceof ArrayAccess ? ($__internal_f10a4cc339617934220127f034125576ed229e948660ebac906a15846d52f136["Server_id"] ?? null) : null), "html", null, true);
            echo "</td>
        <td class=\"right\">";
            // line 101
            echo twig_escape_filter($this->env, ((twig_get_attribute($this->env, $this->source, $context["value"], "Orig_log_pos", [], "array", true, true, false, 101)) ? ((($__internal_887a873a4dc3cf8bd4f99c487b4c7727999c350cc3a772414714e49a195e4386 = $context["value"]) && is_array($__internal_887a873a4dc3cf8bd4f99c487b4c7727999c350cc3a772414714e49a195e4386) || $__internal_887a873a4dc3cf8bd4f99c487b4c7727999c350cc3a772414714e49a195e4386 instanceof ArrayAccess ? ($__internal_887a873a4dc3cf8bd4f99c487b4c7727999c350cc3a772414714e49a195e4386["Orig_log_pos"] ?? null) : null)) : ((($__internal_d527c24a729d38501d770b40a0d25e1ce8a7f0bff897cc4f8f449ba71fcff3d9 = $context["value"]) && is_array($__internal_d527c24a729d38501d770b40a0d25e1ce8a7f0bff897cc4f8f449ba71fcff3d9) || $__internal_d527c24a729d38501d770b40a0d25e1ce8a7f0bff897cc4f8f449ba71fcff3d9 instanceof ArrayAccess ? ($__internal_d527c24a729d38501d770b40a0d25e1ce8a7f0bff897cc4f8f449ba71fcff3d9["End_log_pos"] ?? null) : null))), "html", null, true);
            // line 102
            echo "</td>
        <td>";
            // line 103
            echo PhpMyAdmin\Util::formatSql((($__internal_f6dde3a1020453fdf35e718e94f93ce8eb8803b28cc77a665308e14bbe8572ae = $context["value"]) && is_array($__internal_f6dde3a1020453fdf35e718e94f93ce8eb8803b28cc77a665308e14bbe8572ae) || $__internal_f6dde3a1020453fdf35e718e94f93ce8eb8803b28cc77a665308e14bbe8572ae instanceof ArrayAccess ? ($__internal_f6dde3a1020453fdf35e718e94f93ce8eb8803b28cc77a665308e14bbe8572ae["Info"] ?? null) : null),  !($context["is_full_query"] ?? null));
            echo "</td>
      </tr>
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['value'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 106
        echo "  </tbody>
</table>
";
    }

    public function getTemplateName()
    {
        return "server/binlog/index.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  312 => 106,  303 => 103,  300 => 102,  298 => 101,  294 => 99,  290 => 98,  286 => 97,  282 => 96,  279 => 95,  275 => 94,  267 => 89,  263 => 88,  259 => 87,  255 => 86,  251 => 85,  247 => 84,  242 => 81,  239 => 80,  233 => 77,  228 => 76,  221 => 72,  217 => 71,  215 => 70,  212 => 69,  210 => 68,  207 => 67,  199 => 64,  192 => 63,  184 => 60,  177 => 59,  175 => 58,  172 => 57,  168 => 55,  162 => 52,  157 => 51,  150 => 47,  146 => 46,  143 => 45,  141 => 44,  132 => 38,  125 => 34,  120 => 31,  114 => 29,  112 => 28,  108 => 27,  104 => 26,  101 => 25,  94 => 23,  91 => 22,  89 => 21,  84 => 20,  82 => 19,  78 => 18,  74 => 17,  70 => 16,  66 => 15,  63 => 14,  61 => 13,  57 => 11,  55 => 10,  49 => 7,  44 => 4,  42 => 3,  38 => 2,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "server/binlog/index.twig", "/home/files/phpmyadmin/release/phpMyAdmin-5.0+snapshot/templates/server/binlog/index.twig");
    }
}

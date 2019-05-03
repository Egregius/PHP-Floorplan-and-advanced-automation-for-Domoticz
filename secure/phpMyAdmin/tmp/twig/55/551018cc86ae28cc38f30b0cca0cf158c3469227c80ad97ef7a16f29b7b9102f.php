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

/* server/plugins/index.twig */
class __TwigTemplate_57f6aa28f24f4e90c8c43ef1c3b05b5141c407ea81406182e04ccfbcfe37aa9f extends \Twig\Template
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
        echo PhpMyAdmin\Util::getImage("b_plugin");
        echo "
  ";
        // line 3
        echo _gettext("Plugins");
        // line 4
        echo "</h2>

<div id=\"plugins_plugins\">
  <div id=\"sectionlinks\">
    ";
        // line 8
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(twig_get_array_keys_filter(($context["plugins"] ?? null)));
        foreach ($context['_seq'] as $context["_key"] => $context["plugin_type"]) {
            // line 9
            echo "      <a class=\"btn btn-primary\" href=\"#plugins-";
            echo twig_escape_filter($this->env, (($__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4 = ($context["plugins_type_clean"] ?? null)) && is_array($__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4) || $__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4 instanceof ArrayAccess ? ($__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4[$context["plugin_type"]] ?? null) : null), "html", null, true);
            echo "\">
          ";
            // line 10
            echo twig_escape_filter($this->env, $context["plugin_type"], "html", null, true);
            echo "
      </a>
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['plugin_type'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 13
        echo "  </div>
  ";
        // line 14
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["plugins"] ?? null));
        foreach ($context['_seq'] as $context["plugin_type"] => $context["plugin_list"]) {
            // line 15
            echo "    <div class=\"responsivetable\">
      <table class=\"data_full_width\" id=\"plugins-";
            // line 17
            echo twig_escape_filter($this->env, (($__internal_62824350bc4502ee19dbc2e99fc6bdd3bd90e7d8dd6e72f42c35efd048542144 = ($context["plugins_type_clean"] ?? null)) && is_array($__internal_62824350bc4502ee19dbc2e99fc6bdd3bd90e7d8dd6e72f42c35efd048542144) || $__internal_62824350bc4502ee19dbc2e99fc6bdd3bd90e7d8dd6e72f42c35efd048542144 instanceof ArrayAccess ? ($__internal_62824350bc4502ee19dbc2e99fc6bdd3bd90e7d8dd6e72f42c35efd048542144[$context["plugin_type"]] ?? null) : null), "html", null, true);
            echo "\">
        <caption class=\"tblHeaders\">
          ";
            // line 19
            echo twig_escape_filter($this->env, $context["plugin_type"], "html", null, true);
            echo "
        </caption>
        <thead>
          <tr>
            <th>";
            // line 23
            echo _gettext("Plugin");
            echo "</th>
            <th>";
            // line 24
            echo _gettext("Description");
            echo "</th>
            <th>";
            // line 25
            echo _gettext("Version");
            echo "</th>
            <th>";
            // line 26
            echo _gettext("Author");
            echo "</th>
            <th>";
            // line 27
            echo _gettext("License");
            echo "</th>
          </tr>
        </thead>
        <tbody>
          ";
            // line 31
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($context["plugin_list"]);
            foreach ($context['_seq'] as $context["_key"] => $context["plugin"]) {
                // line 32
                echo "            <tr class=\"noclick\">
              <th>
                ";
                // line 34
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["plugin"], "plugin_name", [], "any", false, false, false, 34), "html", null, true);
                echo "
                ";
                // line 35
                if ( !twig_get_attribute($this->env, $this->source, $context["plugin"], "is_active", [], "any", false, false, false, 35)) {
                    // line 36
                    echo "                  <small class=\"attention\">
                    ";
                    // line 37
                    echo _gettext("disabled");
                    // line 38
                    echo "                  </small>
                ";
                }
                // line 40
                echo "              </th>
              <td>";
                // line 41
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["plugin"], "plugin_description", [], "any", false, false, false, 41), "html", null, true);
                echo "</td>
              <td>";
                // line 42
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["plugin"], "plugin_type_version", [], "any", false, false, false, 42), "html", null, true);
                echo "</td>
              <td>";
                // line 43
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["plugin"], "plugin_author", [], "any", false, false, false, 43), "html", null, true);
                echo "</td>
              <td>";
                // line 44
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["plugin"], "plugin_license", [], "any", false, false, false, 44), "html", null, true);
                echo "</td>
            </tr>
          ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['plugin'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 47
            echo "        </tbody>
      </table>
    </div>
  ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['plugin_type'], $context['plugin_list'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 51
        echo "</div>
";
    }

    public function getTemplateName()
    {
        return "server/plugins/index.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  169 => 51,  160 => 47,  151 => 44,  147 => 43,  143 => 42,  139 => 41,  136 => 40,  132 => 38,  130 => 37,  127 => 36,  125 => 35,  121 => 34,  117 => 32,  113 => 31,  106 => 27,  102 => 26,  98 => 25,  94 => 24,  90 => 23,  83 => 19,  78 => 17,  75 => 15,  71 => 14,  68 => 13,  59 => 10,  54 => 9,  50 => 8,  44 => 4,  42 => 3,  38 => 2,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "server/plugins/index.twig", "/home/files/phpmyadmin/release/phpMyAdmin-5.0+snapshot/templates/server/plugins/index.twig");
    }
}

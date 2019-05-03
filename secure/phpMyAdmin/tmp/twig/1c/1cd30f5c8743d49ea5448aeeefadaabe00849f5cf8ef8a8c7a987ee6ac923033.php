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

/* navigation/main.twig */
class __TwigTemplate_8eaf1e3af74f199dc8238f9db5383f79f4378a2169777935bd04431e9587d076 extends \Twig\Template
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
        if ( !($context["is_ajax"] ?? null)) {
            // line 2
            echo "  <div id=\"pma_navigation\">
    <div id=\"pma_navigation_resizer\"></div>
    <div id=\"pma_navigation_collapser\"></div>
    <div id=\"pma_navigation_content\">
      <div id=\"pma_navigation_header\">
        <a class=\"hide navigation_url\" href=\"navigation.php";
            // line 7
            echo PhpMyAdmin\Url::getCommon(["ajax_request" => true]);
            echo "\"></a>

        ";
            // line 9
            if (twig_get_attribute($this->env, $this->source, ($context["logo"] ?? null), "is_displayed", [], "any", false, false, false, 9)) {
                // line 10
                echo "          <div id=\"pmalogo\">
            ";
                // line 11
                if (twig_get_attribute($this->env, $this->source, ($context["logo"] ?? null), "has_link", [], "any", false, false, false, 11)) {
                    // line 12
                    echo "              <a href=\"";
                    echo twig_escape_filter($this->env, ((twig_get_attribute($this->env, $this->source, ($context["logo"] ?? null), "link", [], "any", true, true, false, 12)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, ($context["logo"] ?? null), "link", [], "any", false, false, false, 12), "#")) : ("#")), "html", null, true);
                    echo "\"";
                    echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["logo"] ?? null), "attributes", [], "any", false, false, false, 12), "html", null, true);
                    echo ">
            ";
                }
                // line 14
                echo "            ";
                if ( !twig_test_empty(twig_get_attribute($this->env, $this->source, ($context["logo"] ?? null), "source", [], "any", false, false, false, 14))) {
                    // line 15
                    echo "              <img id=\"imgpmalogo\" src=\"";
                    echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["logo"] ?? null), "source", [], "any", false, false, false, 15), "html", null, true);
                    echo "\" alt=\"phpMyAdmin\">
            ";
                } else {
                    // line 17
                    echo "              <h1>phpMyAdmin</h1>
            ";
                }
                // line 19
                echo "            ";
                if (twig_get_attribute($this->env, $this->source, ($context["logo"] ?? null), "has_link", [], "any", false, false, false, 19)) {
                    // line 20
                    echo "              </a>
            ";
                }
                // line 22
                echo "          </div>
        ";
            }
            // line 24
            echo "
        <div id=\"navipanellinks\">
          <a href=\"index.php";
            // line 26
            echo PhpMyAdmin\Url::getCommon();
            echo "\" title=\"";
            echo _gettext("Home");
            echo "\">";
            // line 27
            echo PhpMyAdmin\Util::getImage("b_home", _gettext("Home"));
            // line 28
            echo "</a>

          ";
            // line 30
            if ((($context["server"] ?? null) != 0)) {
                // line 31
                echo "            <a class=\"logout disableAjax\" href=\"logout.php";
                echo PhpMyAdmin\Url::getCommon();
                echo "\" title=\"";
                echo twig_escape_filter($this->env, (((($context["auth_type"] ?? null) == "config")) ? (_gettext("Empty session data")) : (_gettext("Log out"))), "html", null, true);
                echo "\">";
                // line 32
                echo PhpMyAdmin\Util::getImage("s_loggoff", (((($context["auth_type"] ?? null) == "config")) ? (_gettext("Empty session data")) : (_gettext("Log out"))));
                // line 33
                echo "</a>
          ";
            }
            // line 35
            echo "
          <a href=\"";
            // line 36
            echo PhpMyAdmin\Util::getDocuLink("index");
            echo "\" title=\"";
            echo _gettext("phpMyAdmin documentation");
            echo "\" target=\"_blank\" rel=\"noopener\">";
            // line 37
            echo PhpMyAdmin\Util::getImage("b_docs", _gettext("phpMyAdmin documentation"));
            // line 38
            echo "</a>

          <a href=\"";
            // line 40
            echo PhpMyAdmin\Util::getMySQLDocuURL("");
            echo "\" title=\"";
            echo _gettext("Documentation");
            echo "\" target=\"_blank\" rel=\"noopener noreferrer\">";
            // line 41
            echo PhpMyAdmin\Util::getImage("b_sqlhelp", _gettext("Documentation"));
            // line 42
            echo "</a>

          <a id=\"pma_navigation_settings_icon\"";
            // line 44
            echo (( !($context["is_navigation_settings_enabled"] ?? null)) ? (" class=\"hide\"") : (""));
            echo " href=\"#\" title=\"";
            echo _gettext("Navigation panel settings");
            echo "\">";
            // line 45
            echo PhpMyAdmin\Util::getImage("s_cog", _gettext("Navigation panel settings"));
            // line 46
            echo "</a>

          <a id=\"pma_navigation_reload\" href=\"#\" title=\"";
            // line 48
            echo _gettext("Reload navigation panel");
            echo "\">";
            // line 49
            echo PhpMyAdmin\Util::getImage("s_reload", _gettext("Reload navigation panel"));
            // line 50
            echo "</a>
        </div>

        ";
            // line 53
            if ((($context["is_servers_displayed"] ?? null) && (twig_length_filter($this->env, ($context["servers"] ?? null)) > 1))) {
                // line 54
                echo "          <div id=\"serverChoice\">
            ";
                // line 55
                echo ($context["server_select"] ?? null);
                echo "
          </div>
        ";
            }
            // line 58
            echo "
        ";
            // line 59
            echo PhpMyAdmin\Util::getImage("ajax_clock_small", _gettext("Loading…"), ["style" => "visibility: hidden; display:none", "class" => "throbber"]);
            // line 62
            echo "
      </div>
      <div id=\"pma_navigation_tree\" class=\"list_container";
            // line 64
            echo ((($context["is_synced"] ?? null)) ? (" synced") : (""));
            echo ((($context["is_highlighted"] ?? null)) ? (" highlight") : (""));
            echo ((($context["is_autoexpanded"] ?? null)) ? (" autoexpand") : (""));
            echo "\">
";
        }
        // line 66
        echo "
";
        // line 67
        if ( !($context["navigation_tree"] ?? null)) {
            // line 68
            echo "  ";
            echo call_user_func_array($this->env->getFilter('error')->getCallable(), [_gettext("An error has occurred while loading the navigation display")]);
            echo "
";
        } else {
            // line 70
            echo "  ";
            echo ($context["navigation_tree"] ?? null);
            echo "
";
        }
        // line 72
        echo "
";
        // line 73
        if ( !($context["is_ajax"] ?? null)) {
            // line 74
            echo "      </div>

      <div id=\"pma_navi_settings_container\">
        ";
            // line 77
            if (($context["is_navigation_settings_enabled"] ?? null)) {
                // line 78
                echo "          ";
                echo ($context["navigation_settings"] ?? null);
                echo "
        ";
            }
            // line 80
            echo "      </div>
    </div>

    ";
            // line 83
            if (($context["is_drag_drop_import_enabled"] ?? null)) {
                // line 84
                echo "      <div class=\"pma_drop_handler\">
        ";
                // line 85
                echo _gettext("Drop files here");
                // line 86
                echo "      </div>
      <div class=\"pma_sql_import_status\">
        <h2>
          ";
                // line 89
                echo _gettext("SQL upload");
                // line 90
                echo "          ( <span class=\"pma_import_count\">0</span> )
          <span class=\"close\">x</span>
          <span class=\"minimize\">-</span>
        </h2>
        <div></div>
      </div>
    ";
            }
            // line 97
            echo "  </div>
";
        }
    }

    public function getTemplateName()
    {
        return "navigation/main.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  253 => 97,  244 => 90,  242 => 89,  237 => 86,  235 => 85,  232 => 84,  230 => 83,  225 => 80,  219 => 78,  217 => 77,  212 => 74,  210 => 73,  207 => 72,  201 => 70,  195 => 68,  193 => 67,  190 => 66,  183 => 64,  179 => 62,  177 => 59,  174 => 58,  168 => 55,  165 => 54,  163 => 53,  158 => 50,  156 => 49,  153 => 48,  149 => 46,  147 => 45,  142 => 44,  138 => 42,  136 => 41,  131 => 40,  127 => 38,  125 => 37,  120 => 36,  117 => 35,  113 => 33,  111 => 32,  105 => 31,  103 => 30,  99 => 28,  97 => 27,  92 => 26,  88 => 24,  84 => 22,  80 => 20,  77 => 19,  73 => 17,  67 => 15,  64 => 14,  56 => 12,  54 => 11,  51 => 10,  49 => 9,  44 => 7,  37 => 2,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "navigation/main.twig", "/var/www/home.egregius.be/secure/phpMyAdmin/templates/navigation/main.twig");
    }
}

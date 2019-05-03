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

/* home/index.twig */
class __TwigTemplate_8f532ee05a2836b15130b65e2fe7f6c81523e6e0b09f40d7a8f6a5de924186f4 extends \Twig\Template
{
    private $source;

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
            '__internal_0c21089f26f6befccc6c25bbaef6f50c1bc7a7754b0cc533041e293b08ad5b23' => [$this, 'block___internal_0c21089f26f6befccc6c25bbaef6f50c1bc7a7754b0cc533041e293b08ad5b23'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        // line 1
        if (($context["is_git_revision"] ?? null)) {
            // line 2
            echo "  <div id=\"is_git_revision\"></div>
";
        }
        // line 4
        echo "
";
        // line 5
        echo ($context["message"] ?? null);
        echo "

";
        // line 7
        echo ($context["partial_logout"] ?? null);
        echo "

<div id=\"maincontainer\">
  ";
        // line 10
        echo ($context["sync_favorite_tables"] ?? null);
        echo "

  <div id=\"main_pane_left\">
    ";
        // line 13
        if (($context["has_server"] ?? null)) {
            // line 14
            echo "      ";
            if (($context["is_demo"] ?? null)) {
                // line 15
                echo "        <div class=\"group\">
          <h2>";
                // line 16
                echo _gettext("phpMyAdmin Demo Server");
                echo "</h2>
          <p class=\"cfg_dbg_demo\">
            ";
                // line 18
                echo sprintf(                $this->renderBlock("__internal_0c21089f26f6befccc6c25bbaef6f50c1bc7a7754b0cc533041e293b08ad5b23", $context, $blocks), "<a href=\"url.php?url=https://demo.phpmyadmin.net/\" target=\"_blank\" rel=\"noopener noreferrer\">demo.phpmyadmin.net</a>");
                // line 23
                echo "          </p>
        </div>
      ";
            }
            // line 26
            echo "
      <div class=\"group\">
        <h2>";
            // line 28
            echo _gettext("General settings");
            echo "</h2>
        <ul>
          ";
            // line 30
            if (($context["has_server_selection"] ?? null)) {
                // line 31
                echo "            <li id=\"li_select_server\" class=\"no_bullets\">
              ";
                // line 32
                echo PhpMyAdmin\Util::getImage("s_host");
                echo "
              ";
                // line 33
                echo ($context["server_selection"] ?? null);
                echo "
            </li>
          ";
            }
            // line 36
            echo "
          ";
            // line 37
            if ((($context["server"] ?? null) > 0)) {
                // line 38
                echo "            ";
                echo (( !twig_test_empty(($context["change_password"] ?? null))) ? (($context["change_password"] ?? null)) : (""));
                echo "

            <li id=\"li_select_mysql_collation\" class=\"no_bullets\">
              <form class=\"disableAjax\" method=\"post\" action=\"index.php\">
                ";
                // line 42
                echo PhpMyAdmin\Url::getHiddenInputs(null, null, 4, "collation_connection");
                echo "
                <label for=\"select_collation_connection\">
                  ";
                // line 44
                echo PhpMyAdmin\Util::getImage("s_asci");
                echo "
                  ";
                // line 45
                echo _gettext("Server connection collation:");
                // line 46
                echo "                  ";
                echo PhpMyAdmin\Util::showMySQLDocu("charset-connection");
                echo "
                </label>
                ";
                // line 48
                echo ($context["server_collation"] ?? null);
                echo "
              </form>
            </li>
          ";
            }
            // line 52
            echo "          ";
            echo (( !twig_test_empty(($context["user_preferences"] ?? null))) ? (($context["user_preferences"] ?? null)) : (""));
            echo "
        </ul>
      </div>
    ";
        }
        // line 56
        echo "
    <div class=\"group\">
      <h2>";
        // line 58
        echo _gettext("Appearance settings");
        echo "</h2>
      <ul>
        ";
        // line 60
        if ( !twig_test_empty(($context["language_selector"] ?? null))) {
            // line 61
            echo "          <li id=\"li_select_lang\" class=\"no_bullets\">
            ";
            // line 62
            echo PhpMyAdmin\Util::getImage("s_lang");
            echo "
            ";
            // line 63
            echo ($context["language_selector"] ?? null);
            echo "
          </li>
        ";
        }
        // line 66
        echo "
        ";
        // line 67
        if ( !twig_test_empty(($context["theme_selection"] ?? null))) {
            // line 68
            echo "          <li id=\"li_select_theme\" class=\"no_bullets\">
            ";
            // line 69
            echo PhpMyAdmin\Util::getImage("s_theme");
            echo "
            ";
            // line 70
            echo ($context["theme_selection"] ?? null);
            echo "
          </li>
        ";
        }
        // line 73
        echo "      </ul>
    </div>
  </div>

  <div id=\"main_pane_right\">
    ";
        // line 78
        if ( !twig_test_empty(($context["database_server"] ?? null))) {
            // line 79
            echo "      <div class=\"group\">
        <h2>";
            // line 80
            echo _gettext("Database server");
            echo "</h2>
        <ul>
          <li id=\"li_server_info\">
            ";
            // line 83
            echo _gettext("Server:");
            // line 84
            echo "            ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["database_server"] ?? null), "host", [], "any", false, false, false, 84), "html", null, true);
            echo "
          </li>
          <li id=\"li_server_type\">
            ";
            // line 87
            echo _gettext("Server type:");
            // line 88
            echo "            ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["database_server"] ?? null), "type", [], "any", false, false, false, 88), "html", null, true);
            echo "
          </li>
          <li id=\"li_server_connection\">
            ";
            // line 91
            echo _gettext("Server connection:");
            // line 92
            echo "            ";
            echo twig_get_attribute($this->env, $this->source, ($context["database_server"] ?? null), "connection", [], "any", false, false, false, 92);
            echo "
          </li>
          <li id=\"li_server_version\">
            ";
            // line 95
            echo _gettext("Server version:");
            // line 96
            echo "            ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["database_server"] ?? null), "version", [], "any", false, false, false, 96), "html", null, true);
            echo "
          </li>
          <li id=\"li_mysql_proto\">
            ";
            // line 99
            echo _gettext("Protocol version:");
            // line 100
            echo "            ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["database_server"] ?? null), "protocol", [], "any", false, false, false, 100), "html", null, true);
            echo "
          </li>
          <li id=\"li_user_info\">
            ";
            // line 103
            echo _gettext("User:");
            // line 104
            echo "            ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["database_server"] ?? null), "user", [], "any", false, false, false, 104), "html", null, true);
            echo "
          </li>
          <li id=\"li_mysql_charset\">
            ";
            // line 107
            echo _gettext("Server charset:");
            // line 108
            echo "            <span lang=\"en\" dir=\"ltr\">
              ";
            // line 109
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["database_server"] ?? null), "charset", [], "any", false, false, false, 109), "html", null, true);
            echo "
            </span>
          </li>
        </ul>
      </div>
    ";
        }
        // line 115
        echo "
    ";
        // line 116
        if (( !twig_test_empty(($context["web_server"] ?? null)) ||  !twig_test_empty(($context["php_info"] ?? null)))) {
            // line 117
            echo "      <div class=\"group\">
        <h2>";
            // line 118
            echo _gettext("Web server");
            echo "</h2>
        <ul>
          ";
            // line 120
            if ( !twig_test_empty(($context["web_server"] ?? null))) {
                // line 121
                echo "            <li id=\"li_web_server_software\">
              ";
                // line 122
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["web_server"] ?? null), "software", [], "any", false, false, false, 122), "html", null, true);
                echo "
            </li>
            <li id=\"li_mysql_client_version\">
              ";
                // line 125
                echo _gettext("Database client version:");
                // line 126
                echo "              ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["web_server"] ?? null), "database", [], "any", false, false, false, 126), "html", null, true);
                echo "
            </li>
            <li id=\"li_used_php_extension\">
              ";
                // line 129
                echo _gettext("PHP extension:");
                // line 130
                echo "              ";
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["web_server"] ?? null), "php_extensions", [], "any", false, false, false, 130));
                foreach ($context['_seq'] as $context["_key"] => $context["extension"]) {
                    // line 131
                    echo "                ";
                    echo twig_escape_filter($this->env, $context["extension"], "html", null, true);
                    echo "
                ";
                    // line 132
                    echo PhpMyAdmin\Util::showPHPDocu((("book." . $context["extension"]) . ".php"));
                    echo "
              ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['extension'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 134
                echo "            </li>
            <li id=\"li_used_php_version\">
              ";
                // line 136
                echo _gettext("PHP version:");
                // line 137
                echo "              ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["web_server"] ?? null), "php_version", [], "any", false, false, false, 137), "html", null, true);
                echo "
            </li>
          ";
            }
            // line 140
            echo "          ";
            if ( !twig_test_empty(($context["php_info"] ?? null))) {
                // line 141
                echo "            ";
                echo ($context["php_info"] ?? null);
                echo "
          ";
            }
            // line 143
            echo "        </ul>
      </div>
    ";
        }
        // line 146
        echo "
    <div class=\"group pmagroup\">
      <h2>phpMyAdmin</h2>
      <ul>
        <li id=\"li_pma_version\"";
        // line 150
        echo ((($context["is_version_checked"] ?? null)) ? (" class=\"jsversioncheck\"") : (""));
        echo ">
          ";
        // line 151
        echo _gettext("Version information:");
        // line 152
        echo "          <span class=\"version\">";
        echo twig_escape_filter($this->env, ($context["phpmyadmin_version"] ?? null), "html", null, true);
        echo "</span>
        </li>
        <li id=\"li_pma_docs\">
          <a href=\"";
        // line 155
        echo PhpMyAdmin\Util::getDocuLink("index");
        echo "\" target=\"_blank\" rel=\"noopener noreferrer\">
            ";
        // line 156
        echo _gettext("Documentation");
        // line 157
        echo "          </a>
        </li>
        <li id=\"li_pma_homepage\">
          <a href=\"";
        // line 160
        echo twig_escape_filter($this->env, PhpMyAdmin\Core::linkURL("https://www.phpmyadmin.net/"), "html", null, true);
        echo "\" target=\"_blank\" rel=\"noopener noreferrer\">
            ";
        // line 161
        echo _gettext("Official Homepage");
        // line 162
        echo "          </a>
        </li>
        <li id=\"li_pma_contribute\">
          <a href=\"";
        // line 165
        echo twig_escape_filter($this->env, PhpMyAdmin\Core::linkURL("https://www.phpmyadmin.net/contribute/"), "html", null, true);
        echo "\" target=\"_blank\" rel=\"noopener noreferrer\">
            ";
        // line 166
        echo _gettext("Contribute");
        // line 167
        echo "          </a>
        </li>
        <li id=\"li_pma_support\">
          <a href=\"";
        // line 170
        echo twig_escape_filter($this->env, PhpMyAdmin\Core::linkURL("https://www.phpmyadmin.net/support/"), "html", null, true);
        echo "\" target=\"_blank\" rel=\"noopener noreferrer\">
            ";
        // line 171
        echo _gettext("Get support");
        // line 172
        echo "          </a>
        </li>
        <li id=\"li_pma_changes\">
          <a href=\"changelog.php";
        // line 175
        echo PhpMyAdmin\Url::getCommon();
        echo "\" target=\"_blank\">
            ";
        // line 176
        echo _gettext("List of changes");
        // line 177
        echo "          </a>
        </li>
        <li id=\"li_pma_license\">
          <a href=\"license.php";
        // line 180
        echo PhpMyAdmin\Url::getCommon();
        echo "\" target=\"_blank\">
            ";
        // line 181
        echo _gettext("License");
        // line 182
        echo "          </a>
        </li>
      </ul>
    </div>
  </div>
</div>

";
        // line 189
        echo ($context["config_storage_message"] ?? null);
        echo "
";
    }

    // line 18
    public function block___internal_0c21089f26f6befccc6c25bbaef6f50c1bc7a7754b0cc533041e293b08ad5b23($context, array $blocks = [])
    {
        // line 19
        echo "              ";
        echo _gettext("You are using the demo server. You can do anything here, but please do not change root, debian-sys-maint and pma users. More information is available at %s.");
        // line 22
        echo "            ";
    }

    public function getTemplateName()
    {
        return "home/index.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  460 => 22,  457 => 19,  454 => 18,  448 => 189,  439 => 182,  437 => 181,  433 => 180,  428 => 177,  426 => 176,  422 => 175,  417 => 172,  415 => 171,  411 => 170,  406 => 167,  404 => 166,  400 => 165,  395 => 162,  393 => 161,  389 => 160,  384 => 157,  382 => 156,  378 => 155,  371 => 152,  369 => 151,  365 => 150,  359 => 146,  354 => 143,  348 => 141,  345 => 140,  338 => 137,  336 => 136,  332 => 134,  324 => 132,  319 => 131,  314 => 130,  312 => 129,  305 => 126,  303 => 125,  297 => 122,  294 => 121,  292 => 120,  287 => 118,  284 => 117,  282 => 116,  279 => 115,  270 => 109,  267 => 108,  265 => 107,  258 => 104,  256 => 103,  249 => 100,  247 => 99,  240 => 96,  238 => 95,  231 => 92,  229 => 91,  222 => 88,  220 => 87,  213 => 84,  211 => 83,  205 => 80,  202 => 79,  200 => 78,  193 => 73,  187 => 70,  183 => 69,  180 => 68,  178 => 67,  175 => 66,  169 => 63,  165 => 62,  162 => 61,  160 => 60,  155 => 58,  151 => 56,  143 => 52,  136 => 48,  130 => 46,  128 => 45,  124 => 44,  119 => 42,  111 => 38,  109 => 37,  106 => 36,  100 => 33,  96 => 32,  93 => 31,  91 => 30,  86 => 28,  82 => 26,  77 => 23,  75 => 18,  70 => 16,  67 => 15,  64 => 14,  62 => 13,  56 => 10,  50 => 7,  45 => 5,  42 => 4,  38 => 2,  36 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "home/index.twig", "/var/www/home.egregius.be/secure/phpMyAdmin/templates/home/index.twig");
    }
}

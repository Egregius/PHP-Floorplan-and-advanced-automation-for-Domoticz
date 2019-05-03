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

/* header.twig */
class __TwigTemplate_e36c95423c04ec94d18b0b5f5b9c523c2662de7c06a665d02967acb5a31f47d2 extends \Twig\Template
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
        if (( !($context["is_ajax"] ?? null) && ($context["is_enabled"] ?? null))) {
            // line 3
            echo "<!doctype html>
<html lang=\"";
            // line 4
            echo twig_escape_filter($this->env, ($context["lang"] ?? null), "html", null, true);
            echo "\" dir=\"";
            echo twig_escape_filter($this->env, ($context["text_dir"] ?? null), "html", null, true);
            echo "\">
<head>
  <meta charset=\"utf-8\">
  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1, shrink-to-fit=no\">
  <meta name=\"referrer\" content=\"no-referrer\">
  <meta name=\"robots\" content=\"noindex,nofollow\">
  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=Edge\">
  ";
            // line 11
            if ( !($context["allow_third_party_framing"] ?? null)) {
                // line 12
                echo "<style id=\"cfs-style\">html{display: none;}</style>";
            }
            // line 14
            echo "
  <link rel=\"icon\" href=\"favicon.ico\" type=\"image/x-icon\">
  <link rel=\"shortcut icon\" href=\"favicon.ico\" type=\"image/x-icon\">
  ";
            // line 17
            if (($context["is_print_view"] ?? null)) {
                // line 18
                echo "    <link rel=\"stylesheet\" type=\"text/css\" href=\"";
                echo twig_escape_filter($this->env, ($context["base_dir"] ?? null), "html", null, true);
                echo "print.css?";
                echo twig_escape_filter($this->env, ($context["version"] ?? null), "html", null, true);
                echo "\">
  ";
            } else {
                // line 20
                echo "    <link rel=\"stylesheet\" type=\"text/css\" href=\"";
                echo twig_escape_filter($this->env, ($context["theme_path"] ?? null), "html", null, true);
                echo "/jquery/jquery-ui.css\">
    <link rel=\"stylesheet\" type=\"text/css\" href=\"";
                // line 21
                echo twig_escape_filter($this->env, ($context["base_dir"] ?? null), "html", null, true);
                echo "js/vendor/codemirror/lib/codemirror.css?";
                echo twig_escape_filter($this->env, ($context["version"] ?? null), "html", null, true);
                echo "\">
    <link rel=\"stylesheet\" type=\"text/css\" href=\"";
                // line 22
                echo twig_escape_filter($this->env, ($context["base_dir"] ?? null), "html", null, true);
                echo "js/vendor/codemirror/addon/hint/show-hint.css?";
                echo twig_escape_filter($this->env, ($context["version"] ?? null), "html", null, true);
                echo "\">
    <link rel=\"stylesheet\" type=\"text/css\" href=\"";
                // line 23
                echo twig_escape_filter($this->env, ($context["base_dir"] ?? null), "html", null, true);
                echo "js/vendor/codemirror/addon/lint/lint.css?";
                echo twig_escape_filter($this->env, ($context["version"] ?? null), "html", null, true);
                echo "\">
    <link rel=\"stylesheet\" type=\"text/css\" href=\"";
                // line 24
                echo twig_escape_filter($this->env, ($context["theme_path"] ?? null), "html", null, true);
                echo "/css/theme";
                echo (((($context["text_dir"] ?? null) == "rtl")) ? ("-rtl") : (""));
                echo ".css?";
                echo twig_escape_filter($this->env, ($context["version"] ?? null), "html", null, true);
                echo "&nocache=";
                // line 25
                echo twig_escape_filter($this->env, ($context["unique_value"] ?? null), "html", null, true);
                echo twig_escape_filter($this->env, ($context["text_dir"] ?? null), "html", null, true);
                if ( !twig_test_empty(($context["server"] ?? null))) {
                    echo "&server=";
                    echo twig_escape_filter($this->env, ($context["server"] ?? null), "html", null, true);
                }
                echo "\">
    <link rel=\"stylesheet\" type=\"text/css\" href=\"";
                // line 26
                echo twig_escape_filter($this->env, ($context["theme_path"] ?? null), "html", null, true);
                echo "/css/printview.css?";
                echo twig_escape_filter($this->env, ($context["version"] ?? null), "html", null, true);
                echo "\" media=\"print\" id=\"printcss\">
  ";
            }
            // line 28
            echo "  <title>";
            echo twig_escape_filter($this->env, ($context["title"] ?? null), "html", null, true);
            echo "</title>
  ";
            // line 29
            echo ($context["scripts"] ?? null);
            echo "
  <noscript><style>html{display:block}</style></noscript>
</head>
<body";
            // line 32
            (( !twig_test_empty(($context["body_id"] ?? null))) ? (print (twig_escape_filter($this->env, (" id=" . ($context["body_id"] ?? null)), "html", null, true))) : (print ("")));
            echo ">
  ";
            // line 33
            echo ($context["navigation"] ?? null);
            echo "
  ";
            // line 34
            echo ($context["custom_header"] ?? null);
            echo "
  ";
            // line 35
            echo ($context["load_user_preferences"] ?? null);
            echo "

  ";
            // line 37
            if ( !($context["show_hint"] ?? null)) {
                // line 38
                echo "    <span id=\"no_hint\" class=\"hide\"></span>
  ";
            }
            // line 40
            echo "
  ";
            // line 41
            if (($context["is_warnings_enabled"] ?? null)) {
                // line 42
                echo "    <noscript>
      ";
                // line 43
                echo call_user_func_array($this->env->getFilter('error')->getCallable(), [_gettext("Javascript must be enabled past this point!")]);
                echo "
    </noscript>
  ";
            }
            // line 46
            echo "
  ";
            // line 47
            if ((($context["is_menu_enabled"] ?? null) && (($context["server"] ?? null) > 0))) {
                // line 48
                echo "    ";
                echo ($context["menu"] ?? null);
                echo "
    <span id=\"page_nav_icons\">
      <span id=\"lock_page_icon\"></span>
      <span id=\"page_settings_icon\">
        ";
                // line 52
                echo PhpMyAdmin\Util::getImage("s_cog", _gettext("Page-related settings"));
                echo "
      </span>
      <a id=\"goto_pagetop\" href=\"#\">";
                // line 54
                echo PhpMyAdmin\Util::getImage("s_top", _gettext("Click on the bar to scroll to top of page"));
                echo "</a>
    </span>
  ";
            }
            // line 57
            echo "
  ";
            // line 58
            echo ($context["console"] ?? null);
            echo "

  <div id=\"page_content\">
    ";
            // line 61
            echo ($context["messages"] ?? null);
            echo "
";
        }
        // line 63
        echo "
";
        // line 64
        if ((($context["is_enabled"] ?? null) && ($context["has_recent_table"] ?? null))) {
            // line 65
            echo "  ";
            echo ($context["recent_table"] ?? null);
            echo "
";
        }
    }

    public function getTemplateName()
    {
        return "header.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  209 => 65,  207 => 64,  204 => 63,  199 => 61,  193 => 58,  190 => 57,  184 => 54,  179 => 52,  171 => 48,  169 => 47,  166 => 46,  160 => 43,  157 => 42,  155 => 41,  152 => 40,  148 => 38,  146 => 37,  141 => 35,  137 => 34,  133 => 33,  129 => 32,  123 => 29,  118 => 28,  111 => 26,  102 => 25,  95 => 24,  89 => 23,  83 => 22,  77 => 21,  72 => 20,  64 => 18,  62 => 17,  57 => 14,  54 => 12,  52 => 11,  40 => 4,  37 => 3,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "header.twig", "/var/www/home.egregius.be/secure/phpMyAdmin/templates/header.twig");
    }
}

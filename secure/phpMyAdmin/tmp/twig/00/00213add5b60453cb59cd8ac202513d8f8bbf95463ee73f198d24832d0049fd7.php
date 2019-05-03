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

/* server/replication/master_add_slave_user.twig */
class __TwigTemplate_5865895638ce8704f2e2735c658ed1627c7f95224c109e340d7fb6b08fe830cb extends \Twig\Template
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
        echo "<div id=\"master_addslaveuser_gui\">
  <form action=\"server_privileges.php\" method=\"post\" autocomplete=\"off\" onsubmit=\"return checkAddUser(this);\">
    ";
        // line 3
        echo PhpMyAdmin\Url::getHiddenInputs("", "");
        echo "

    <fieldset id=\"fieldset_add_user_login\">
      <legend>";
        // line 6
        echo _gettext("Add slave replication user");
        echo "</legend>

      <input type=\"hidden\" name=\"grant_count\" value=\"25\">
      <input type=\"hidden\" name=\"createdb\" id=\"createdb_0\" value=\"0\">
      ";
        // line 11
        echo "      <input type=\"hidden\" name=\"Repl_slave_priv\" id=\"checkbox_Repl_slave_priv\" value=\"Y\">
      <input type=\"hidden\" name=\"sr_take_action\" value=\"true\">

      <div class=\"item\">
        <label for=\"select_pred_username\">
          ";
        // line 16
        echo _gettext("User name:");
        // line 17
        echo "        </label>
        <span class=\"options\">
          <select name=\"pred_username\" id=\"select_pred_username\" title=\"";
        // line 19
        echo _gettext("User name");
        echo "\">
            <option value=\"any\"";
        // line 20
        echo (((($context["predefined_username"] ?? null) == "any")) ? (" selected") : (""));
        echo ">";
        echo _gettext("Any user");
        echo "</option>
            <option value=\"userdefined\"";
        // line 21
        echo (((($context["predefined_username"] ?? null) == "userdefined")) ? (" selected") : (""));
        echo ">";
        echo _gettext("Use text field:");
        echo "</option>
          </select>
        </span>
        <input type=\"text\" name=\"username\" id=\"pma_username\" maxlength=\"";
        // line 24
        echo twig_escape_filter($this->env, ($context["username_length"] ?? null), "html", null, true);
        echo "\" title=\"";
        echo _gettext("User name");
        echo "\" value=\"";
        echo twig_escape_filter($this->env, ($context["username"] ?? null), "html", null, true);
        echo "\">
      </div>

      <div class=\"item\">
        <label for=\"select_pred_hostname\">
          ";
        // line 29
        echo _gettext("Host:");
        // line 30
        echo "        </label>
        <span class=\"options\">
          <select name=\"pred_hostname\" id=\"select_pred_hostname\" title=\"";
        // line 32
        echo _gettext("Host");
        echo "\"";
        // line 33
        if ( !(null === ($context["this_host"] ?? null))) {
            echo " data-thishost=\"";
            echo twig_escape_filter($this->env, ($context["this_host"] ?? null), "html", null, true);
            echo "\"";
        }
        echo ">
            <option value=\"any\"";
        // line 34
        echo (((($context["predefined_hostname"] ?? null) == "any")) ? (" selected") : (""));
        echo ">";
        echo _gettext("Any host");
        echo "</option>
            <option value=\"localhost\"";
        // line 35
        echo (((($context["predefined_hostname"] ?? null) == "localhost")) ? (" selected") : (""));
        echo ">";
        echo _gettext("Local");
        echo "</option>
            ";
        // line 36
        if ( !(null === ($context["this_host"] ?? null))) {
            // line 37
            echo "              <option value=\"thishost\"";
            echo (((($context["predefined_hostname"] ?? null) == "thishost")) ? (" selected") : (""));
            echo ">";
            echo _gettext("This host");
            echo "</option>
            ";
        }
        // line 39
        echo "            <option value=\"hosttable\"";
        echo (((($context["predefined_hostname"] ?? null) == "hosttable")) ? (" selected") : (""));
        echo ">";
        echo _gettext("Use host table");
        echo "</option>
            <option value=\"userdefined\"";
        // line 40
        echo (((($context["predefined_hostname"] ?? null) == "userdefined")) ? (" selected") : (""));
        echo ">";
        echo _gettext("Use text field:");
        echo "</option>
          </select>
        </span>
        <input type=\"text\" name=\"hostname\" id=\"pma_hostname\" maxlength=\"";
        // line 43
        echo twig_escape_filter($this->env, ($context["hostname_length"] ?? null), "html", null, true);
        echo "\" title=\"";
        echo _gettext("Host");
        echo "\" value=\"";
        echo twig_escape_filter($this->env, ($context["hostname"] ?? null), "html", null, true);
        echo "\">
        ";
        // line 44
        echo PhpMyAdmin\Util::showHint(_gettext("When Host table is used, this field is ignored and values stored in Host table are used instead."));
        echo "
      </div>

      <div class=\"item\">
        <label for=\"select_pred_password\">
          ";
        // line 49
        echo _gettext("Password:");
        // line 50
        echo "        </label>
        <span class=\"options\">
          <select name=\"pred_password\" id=\"select_pred_password\" title=\"";
        // line 52
        echo _gettext("Password");
        echo "\">
            <option value=\"none\"";
        // line 53
        echo ((($context["has_username"] ?? null)) ? (" selected") : (""));
        echo ">";
        echo _gettext("No password");
        echo "</option>
            <option value=\"userdefined\"";
        // line 54
        echo (( !($context["has_username"] ?? null)) ? (" selected") : (""));
        echo ">";
        echo _gettext("Use text field:");
        echo "</option>
          </select>
        </span>
        <input type=\"password\" id=\"text_pma_pw\" name=\"pma_pw\" title=\"";
        // line 57
        echo _gettext("Password");
        echo "\">
      </div>

      <div class=\"item\">
        <label for=\"text_pma_pw2\">
          ";
        // line 62
        echo _gettext("Re-type:");
        // line 63
        echo "        </label>
        <span class=\"options\"></span>
        <input type=\"password\" id=\"text_pma_pw2\" name=\"pma_pw2\" title=\"";
        // line 65
        echo _gettext("Re-type");
        echo "\">
      </div>

      <div class=\"item\">
        <label for=\"button_generate_password\">
          ";
        // line 70
        echo _gettext("Generate password:");
        // line 71
        echo "        </label>
        <span class=\"options\">
          <input type=\"button\" class=\"btn btn-secondary button\" id=\"button_generate_password\" value=\"";
        // line 73
        echo _gettext("Generate");
        echo "\" onclick=\"suggestPassword(this.form)\">
        </span>
        <input type=\"text\" name=\"generated_pw\" id=\"generated_pw\">
      </div>
    </fieldset>

    <fieldset id=\"fieldset_user_privtable_footer\" class=\"tblFooters\">
      <input type=\"hidden\" name=\"adduser_submit\" value=\"1\">
      <input class=\"btn btn-primary\" type=\"submit\" id=\"adduser_submit\" value=\"";
        // line 81
        echo _gettext("Go");
        echo "\">
    </fieldset>
  </form>
</div>
";
    }

    public function getTemplateName()
    {
        return "server/replication/master_add_slave_user.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  228 => 81,  217 => 73,  213 => 71,  211 => 70,  203 => 65,  199 => 63,  197 => 62,  189 => 57,  181 => 54,  175 => 53,  171 => 52,  167 => 50,  165 => 49,  157 => 44,  149 => 43,  141 => 40,  134 => 39,  126 => 37,  124 => 36,  118 => 35,  112 => 34,  104 => 33,  101 => 32,  97 => 30,  95 => 29,  83 => 24,  75 => 21,  69 => 20,  65 => 19,  61 => 17,  59 => 16,  52 => 11,  45 => 6,  39 => 3,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "server/replication/master_add_slave_user.twig", "/home/files/phpmyadmin/release/phpMyAdmin-5.0+snapshot/templates/server/replication/master_add_slave_user.twig");
    }
}

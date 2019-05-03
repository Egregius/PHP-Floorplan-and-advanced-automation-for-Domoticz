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

/* server/replication/change_master.twig */
class __TwigTemplate_3abcb81b00003e9ffc794d0300c876db8481819a8320793091f9133970aeb5e5 extends \Twig\Template
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
        echo "<form method=\"post\" action=\"server_replication.php\">
  ";
        // line 2
        echo PhpMyAdmin\Url::getHiddenInputs("", "");
        echo "
  <fieldset id=\"fieldset_add_user_login\">
    <legend>
      ";
        // line 5
        echo _gettext("Slave configuration");
        echo " -
      ";
        // line 6
        echo _gettext("Change or reconfigure master server");
        // line 7
        echo "    </legend>
    <p>
      ";
        // line 9
        echo _gettext("Make sure you have a unique server-id in your configuration file (my.cnf). If not, please add the following line into [mysqld] section:");
        // line 10
        echo "    </p>
    <pre>server-id=";
        // line 11
        echo twig_escape_filter($this->env, ($context["server_id"] ?? null), "html", null, true);
        echo "</pre>

    <div class=\"item\">
      <label for=\"text_username\">";
        // line 14
        echo _gettext("User name:");
        echo "</label>
      <input id=\"text_username\" name=\"username\" type=\"text\" maxlength=\"";
        // line 15
        echo twig_escape_filter($this->env, ($context["username_length"] ?? null), "html", null, true);
        echo "\" title=\"";
        echo _gettext("User name");
        echo "\" required>
    </div>
    <div class=\"item\">
      <label for=\"text_pma_pw\">";
        // line 18
        echo _gettext("Password:");
        echo "</label>
      <input id=\"text_pma_pw\" name=\"pma_pw\" type=\"password\" title=\"";
        // line 19
        echo _gettext("Password");
        echo "\" required>
    </div>
    <div class=\"item\">
      <label for=\"text_hostname\">";
        // line 22
        echo _gettext("Host:");
        echo "</label>
      <input id=\"text_hostname\" name=\"hostname\" type=\"text\" maxlength=\"";
        // line 23
        echo twig_escape_filter($this->env, ($context["hostname_length"] ?? null), "html", null, true);
        echo "\" value=\"\" required>
    </div>
    <div class=\"item\">
      <label for=\"text_port\">";
        // line 26
        echo _gettext("Port:");
        echo "</label>
      <input id=\"text_port\" name=\"text_port\" type=\"number\" maxlength=\"6\" value=\"3306\" required>
    </div>
  </fieldset>
  <fieldset id=\"fieldset_user_privtable_footer\" class=\"tblFooters\">
    <input type=\"hidden\" name=\"sr_take_action\" value=\"true\">
    <input type=\"hidden\" name=\"";
        // line 32
        echo twig_escape_filter($this->env, ($context["submit_name"] ?? null), "html", null, true);
        echo "\" value=\"1\">
    <input class=\"btn btn-primary\" type=\"submit\" id=\"confslave_submit\" value=\"";
        // line 33
        echo _gettext("Go");
        echo "\">
  </fieldset>
</form>
";
    }

    public function getTemplateName()
    {
        return "server/replication/change_master.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  110 => 33,  106 => 32,  97 => 26,  91 => 23,  87 => 22,  81 => 19,  77 => 18,  69 => 15,  65 => 14,  59 => 11,  56 => 10,  54 => 9,  50 => 7,  48 => 6,  44 => 5,  38 => 2,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "server/replication/change_master.twig", "/home/files/phpmyadmin/release/phpMyAdmin-5.0+snapshot/templates/server/replication/change_master.twig");
    }
}

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

/* server/privileges/choose_user_group.twig */
class __TwigTemplate_d83ee696edd9abe9f9cccd5453c90ea1a68088c4589e16daa0415a545a400741 extends \Twig\Template
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
        echo "<form class=\"ajax\" id=\"changeUserGroupForm\" action=\"server_privileges.php\" method=\"post\">
    ";
        // line 2
        echo PhpMyAdmin\Url::getHiddenInputs(($context["params"] ?? null));
        echo "
    <fieldset id=\"fieldset_user_group_selection\">
        <legend>";
        // line 4
        echo _gettext("User group");
        echo "</legend>
        ";
        // line 5
        echo _gettext("User group");
        echo ":
        ";
        // line 6
        echo PhpMyAdmin\Util::getDropdown("userGroup", ($context["all_user_groups"] ?? null), ($context["user_group"] ?? null), "userGroup_select");
        echo "
        <input type=\"hidden\" name=\"changeUserGroup\" value=\"1\">
    </fieldset>
</form>
";
    }

    public function getTemplateName()
    {
        return "server/privileges/choose_user_group.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  51 => 6,  47 => 5,  43 => 4,  38 => 2,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "server/privileges/choose_user_group.twig", "/home/files/phpmyadmin/release/phpMyAdmin-5.0+snapshot/templates/server/privileges/choose_user_group.twig");
    }
}

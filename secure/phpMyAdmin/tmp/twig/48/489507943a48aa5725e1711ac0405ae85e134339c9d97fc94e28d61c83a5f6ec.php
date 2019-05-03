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

/* server/privileges/delete_user_fieldset.twig */
class __TwigTemplate_35ffa029e4d3ed035745ff2d0c5438f94f08fcd3070ef2f9df29065f299489a6 extends \Twig\Template
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
        echo "<fieldset id=\"fieldset_delete_user\">
    <legend>
        ";
        // line 3
        echo PhpMyAdmin\Util::getIcon("b_usrdrop");
        echo _gettext("Remove selected user accounts");
        // line 4
        echo "    </legend>
    <input type=\"hidden\" name=\"mode\" value=\"2\">
    <p>(";
        // line 6
        echo _gettext("Revoke all active privileges from the users and delete them afterwards.");
        echo ")</p>
    <input type=\"checkbox\" title=\"";
        // line 7
        echo _gettext("Drop the databases that have the same names as the users.");
        echo "\"
        name=\"drop_users_db\" id=\"checkbox_drop_users_db\">
    <label for=\"checkbox_drop_users_db\"
        title=\"";
        // line 10
        echo _gettext("Drop the databases that have the same names as the users.");
        echo "\">
        ";
        // line 11
        echo _gettext("Drop the databases that have the same names as the users.");
        // line 12
        echo "    </label>
</fieldset>

<fieldset id=\"fieldset_delete_user_footer\" class=\"tblFooters\">
    <input id=\"buttonGo\" class=\"btn btn-primary ajax\" type=\"submit\" name=\"delete\" value=\"";
        // line 16
        echo _gettext("Go");
        echo "\">
</fieldset>
";
    }

    public function getTemplateName()
    {
        return "server/privileges/delete_user_fieldset.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  68 => 16,  62 => 12,  60 => 11,  56 => 10,  50 => 7,  46 => 6,  42 => 4,  39 => 3,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "server/privileges/delete_user_fieldset.twig", "/home/files/phpmyadmin/release/phpMyAdmin-5.0+snapshot/templates/server/privileges/delete_user_fieldset.twig");
    }
}

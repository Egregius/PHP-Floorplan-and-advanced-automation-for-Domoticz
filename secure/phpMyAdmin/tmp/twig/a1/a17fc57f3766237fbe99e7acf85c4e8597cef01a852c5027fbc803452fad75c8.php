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

/* database/designer/edit_delete_pages.twig */
class __TwigTemplate_c24b93960786b20fa19dea17f7cde577dcb3d6ff638226335855d05fbabec5e8 extends \Twig\Template
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
        echo "<form action=\"db_designer.php\" method=\"post\" name=\"edit_delete_pages\" id=\"edit_delete_pages\" class=\"ajax\">
    ";
        // line 2
        echo PhpMyAdmin\Url::getHiddenInputs(($context["db"] ?? null));
        echo "
    <fieldset id=\"page_edit_delete_options\">
        <input type=\"hidden\" name=\"operation\" value=\"";
        // line 4
        echo twig_escape_filter($this->env, ($context["operation"] ?? null), "html", null, true);
        echo "\">
        <label for=\"selected_page\">
            ";
        // line 6
        echo twig_escape_filter($this->env, (((($context["operation"] ?? null) == "editPage")) ? (_gettext("Page to open")) : (_gettext("Page to delete"))), "html", null, true);
        echo ":
        </label>
        ";
        // line 8
        $this->loadTemplate("database/designer/page_selector.twig", "database/designer/edit_delete_pages.twig", 8)->display(twig_to_array(["pdfwork" =>         // line 9
($context["pdfwork"] ?? null), "pages" =>         // line 10
($context["pages"] ?? null)]));
        // line 12
        echo "    </fieldset>
</form>
";
    }

    public function getTemplateName()
    {
        return "database/designer/edit_delete_pages.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  57 => 12,  55 => 10,  54 => 9,  53 => 8,  48 => 6,  43 => 4,  38 => 2,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "database/designer/edit_delete_pages.twig", "/home/files/phpmyadmin/release/phpMyAdmin-5.0+snapshot/templates/database/designer/edit_delete_pages.twig");
    }
}

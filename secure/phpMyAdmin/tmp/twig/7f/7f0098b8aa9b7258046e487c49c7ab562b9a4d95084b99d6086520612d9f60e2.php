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

/* display/results/table_headers.twig */
class __TwigTemplate_14f57fca8493effa0915971af33afd0c10b4e5f71653a47b26a4fdb17ff861b4 extends \Twig\Template
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
        echo "<input class=\"save_cells_at_once\" type=\"hidden\" value=\"";
        echo twig_escape_filter($this->env, ($context["save_cells_at_once"] ?? null), "html", null, true);
        echo "\">
<div class=\"common_hidden_inputs\">
  ";
        // line 3
        echo PhpMyAdmin\Url::getHiddenInputs(($context["db"] ?? null), ($context["table"] ?? null));
        echo "
</div>

";
        // line 6
        echo ($context["data_for_resetting_column_order"] ?? null);
        echo "
";
        // line 7
        echo ($context["options_block"] ?? null);
        echo "

";
        // line 9
        if (((($context["delete_link"] ?? null) == ($context["delete_row"] ?? null)) || (($context["delete_link"] ?? null) == ($context["kill_process"] ?? null)))) {
            // line 10
            echo "  <form method=\"post\" action=\"tbl_row_action.php\" name=\"resultsForm\" id=\"resultsForm_";
            echo twig_escape_filter($this->env, ($context["unique_id"] ?? null), "html", null, true);
            echo "\" class=\"ajax\">
    ";
            // line 11
            echo PhpMyAdmin\Url::getHiddenInputs(($context["db"] ?? null), ($context["table"] ?? null), 1);
            echo "
    <input type=\"hidden\" name=\"goto\" value=\"sql.php\">
";
        }
        // line 14
        echo "
<div class=\"responsivetable\">
  <table class=\"table_results data ajax\" data-uniqueId=\"";
        // line 16
        echo twig_escape_filter($this->env, ($context["unique_id"] ?? null), "html", null, true);
        echo "\">

    ";
        // line 18
        echo ($context["button"] ?? null);
        echo "
    ";
        // line 19
        echo ($context["table_headers_for_columns"] ?? null);
        echo "
    ";
        // line 20
        echo ($context["column_at_right_side"] ?? null);
        echo "

      </tr>
    </thead>
";
    }

    public function getTemplateName()
    {
        return "display/results/table_headers.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  86 => 20,  82 => 19,  78 => 18,  73 => 16,  69 => 14,  63 => 11,  58 => 10,  56 => 9,  51 => 7,  47 => 6,  41 => 3,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "display/results/table_headers.twig", "/var/www/home.egregius.be/secure/phpMyAdmin/templates/display/results/table_headers.twig");
    }
}

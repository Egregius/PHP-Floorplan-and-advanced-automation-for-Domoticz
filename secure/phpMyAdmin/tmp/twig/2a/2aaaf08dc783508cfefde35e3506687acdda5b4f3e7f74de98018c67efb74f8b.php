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

/* server/privileges/initials_row.twig */
class __TwigTemplate_cfa7682a27f78dea912e13520c2738040866efbfb7dee68c4ee2dbd5c95e3b6e extends \Twig\Template
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
        echo "<table id=\"initials_table\" cellspacing=\"5\">
    <tr>
        ";
        // line 3
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["array_initials"] ?? null));
        foreach ($context['_seq'] as $context["tmp_initial"] => $context["initial_was_found"]) {
            if ( !($context["tmp_initial"] === null)) {
                // line 4
                echo "            ";
                if ($context["initial_was_found"]) {
                    // line 5
                    echo "                <td>
                    <a class=\"ajax";
                    // line 7
                    echo ((((isset($context["initial"]) || array_key_exists("initial", $context)) && (($context["initial"] ?? null) === $context["tmp_initial"]))) ? (" active") : (""));
                    // line 8
                    echo "\" href=\"server_privileges.php";
                    // line 9
                    echo PhpMyAdmin\Url::getCommon(["initial" => $context["tmp_initial"]]);
                    echo "\">";
                    // line 10
                    echo $context["tmp_initial"];
                    // line 11
                    echo "</a>
                </td>
            ";
                } else {
                    // line 14
                    echo "                <td>";
                    echo $context["tmp_initial"];
                    echo "</td>
            ";
                }
                // line 16
                echo "        ";
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['tmp_initial'], $context['initial_was_found'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 17
        echo "        <td>
            <a href=\"server_privileges.php";
        // line 19
        echo PhpMyAdmin\Url::getCommon(["showall" => 1]);
        echo "\" class=\"nowrap\">
                ";
        // line 20
        echo _gettext("Show all");
        // line 21
        echo "            </a>
        </td>
    </tr>
</table>
";
    }

    public function getTemplateName()
    {
        return "server/privileges/initials_row.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  86 => 21,  84 => 20,  80 => 19,  77 => 17,  70 => 16,  64 => 14,  59 => 11,  57 => 10,  54 => 9,  52 => 8,  50 => 7,  47 => 5,  44 => 4,  39 => 3,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "server/privileges/initials_row.twig", "/home/files/phpmyadmin/release/phpMyAdmin-5.0+snapshot/templates/server/privileges/initials_row.twig");
    }
}

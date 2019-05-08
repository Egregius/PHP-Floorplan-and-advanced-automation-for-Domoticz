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

/* list/unordered.twig */
class __TwigTemplate_ff141ab342c9f0f927c7ffeb293eb5d8b57d116a6f76cc1eb6827cf8286e3bfb extends \Twig\Template
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
        echo "<ul";
        if ( !twig_test_empty(($context["id"] ?? null))) {
            echo " id=\"";
            echo twig_escape_filter($this->env, ($context["id"] ?? null), "html", null, true);
            echo "\"";
        }
        // line 2
        if ( !twig_test_empty(($context["class"] ?? null))) {
            echo " class=\"";
            echo twig_escape_filter($this->env, ($context["class"] ?? null), "html", null, true);
            echo "\"";
        }
        echo ">

    ";
        // line 4
        if ( !twig_test_empty(($context["items"] ?? null))) {
            // line 5
            echo "        ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["items"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["item"]) {
                // line 6
                echo "            ";
                if ( !twig_test_iterable($context["item"])) {
                    // line 7
                    echo "                ";
                    $context["item"] = ["content" => $context["item"]];
                    // line 8
                    echo "            ";
                }
                // line 9
                echo "            ";
                $this->loadTemplate("list/item.twig", "list/unordered.twig", 9)->display(twig_to_array($context["item"]));
                // line 10
                echo "        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['item'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 11
            echo "    ";
        } elseif ( !twig_test_empty(($context["content"] ?? null))) {
            // line 12
            echo "        ";
            echo ($context["content"] ?? null);
            echo "
    ";
        }
        // line 14
        echo "</ul>
";
    }

    public function getTemplateName()
    {
        return "list/unordered.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  85 => 14,  79 => 12,  76 => 11,  70 => 10,  67 => 9,  64 => 8,  61 => 7,  58 => 6,  53 => 5,  51 => 4,  42 => 2,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "list/unordered.twig", "/var/www/html/secure/phpMyAdmin/templates/list/unordered.twig");
    }
}

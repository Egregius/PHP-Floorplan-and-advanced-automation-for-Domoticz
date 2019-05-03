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

/* scripts.twig */
class __TwigTemplate_36d4003d385586debd96bf78d9abe3c1e204a4073f590bbc5c18e0c5b0ef23a2 extends \Twig\Template
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
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["files"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["file"]) {
            // line 2
            echo "  <script data-cfasync=\"false\" type=\"text/javascript\" src=\"js/";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["file"], "filename", [], "any", false, false, false, 2), "html", null, true);
            // line 3
            ((twig_in_filter(".php", twig_get_attribute($this->env, $this->source, $context["file"], "filename", [], "any", false, false, false, 3))) ? (print (PhpMyAdmin\Url::getCommon(twig_array_merge(twig_get_attribute($this->env, $this->source, $context["file"], "params", [], "any", false, false, false, 3), ["v" => ($context["version"] ?? null)])))) : (print (twig_escape_filter($this->env, ("?v=" . twig_urlencode_filter(($context["version"] ?? null))), "html", null, true))));
            echo "\"></script>
";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['file'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 5
        echo "
<script data-cfasync=\"false\" type=\"text/javascript\">
// <![CDATA[
";
        // line 8
        echo ($context["code"] ?? null);
        echo "
";
        // line 9
        if ( !twig_test_empty(($context["files"] ?? null))) {
            // line 10
            echo "AJAX.scriptHandler
";
            // line 11
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["files"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["file"]) {
                // line 12
                echo "  .add('";
                echo PhpMyAdmin\Sanitize::escapeJsString(twig_get_attribute($this->env, $this->source, $context["file"], "filename", [], "any", false, false, false, 12));
                echo "', ";
                echo ((twig_get_attribute($this->env, $this->source, $context["file"], "has_onload", [], "any", false, false, false, 12)) ? (1) : (0));
                echo ")
";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['file'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 14
            echo ";
\$(function() {
";
            // line 16
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["files"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["file"]) {
                if (twig_get_attribute($this->env, $this->source, $context["file"], "has_onload", [], "any", false, false, false, 16)) {
                    // line 17
                    echo "  AJAX.fireOnload('";
                    echo PhpMyAdmin\Sanitize::escapeJsString(twig_get_attribute($this->env, $this->source, $context["file"], "filename", [], "any", false, false, false, 17));
                    echo "');
";
                }
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['file'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 19
            echo "});
";
        }
        // line 21
        echo "// ]]>
</script>
";
    }

    public function getTemplateName()
    {
        return "scripts.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  102 => 21,  98 => 19,  88 => 17,  83 => 16,  79 => 14,  68 => 12,  64 => 11,  61 => 10,  59 => 9,  55 => 8,  50 => 5,  42 => 3,  39 => 2,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "scripts.twig", "/var/www/home.egregius.be/secure/phpMyAdmin/templates/scripts.twig");
    }
}

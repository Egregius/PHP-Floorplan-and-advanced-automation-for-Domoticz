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

/* navigation/item_unhide_dialog.twig */
class __TwigTemplate_0035b4e935ee7f573e35414a4752ccc7b9513e16b2a8f2bcd20fb31741960fba extends \Twig\Template
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
        echo "<form class=\"ajax\" action=\"navigation.php\" method=\"post\">
  <fieldset>
    ";
        // line 3
        echo PhpMyAdmin\Url::getHiddenInputs(($context["database"] ?? null), ($context["table"] ?? null));
        echo "

    ";
        // line 5
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["types"] ?? null));
        $context['loop'] = [
          'parent' => $context['_parent'],
          'index0' => 0,
          'index'  => 1,
          'first'  => true,
        ];
        foreach ($context['_seq'] as $context["type"] => $context["label"]) {
            if (((twig_test_empty(($context["item_type"] ?? null)) || (($context["item_type"] ?? null) == $context["type"])) && twig_test_iterable((($__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4 = ($context["hidden"] ?? null)) && is_array($__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4) || $__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4 instanceof ArrayAccess ? ($__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4[$context["type"]] ?? null) : null)))) {
                // line 6
                echo "      ";
                echo (( !twig_get_attribute($this->env, $this->source, $context["loop"], "first", [], "any", false, false, false, 6)) ? ("<br>") : (""));
                echo "
      <strong>";
                // line 7
                echo twig_escape_filter($this->env, $context["label"], "html", null, true);
                echo "</strong>
      <table class=\"all100\">
        <tbody>
          ";
                // line 10
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable((($__internal_62824350bc4502ee19dbc2e99fc6bdd3bd90e7d8dd6e72f42c35efd048542144 = ($context["hidden"] ?? null)) && is_array($__internal_62824350bc4502ee19dbc2e99fc6bdd3bd90e7d8dd6e72f42c35efd048542144) || $__internal_62824350bc4502ee19dbc2e99fc6bdd3bd90e7d8dd6e72f42c35efd048542144 instanceof ArrayAccess ? ($__internal_62824350bc4502ee19dbc2e99fc6bdd3bd90e7d8dd6e72f42c35efd048542144[$context["type"]] ?? null) : null));
                foreach ($context['_seq'] as $context["_key"] => $context["item"]) {
                    // line 11
                    echo "            <tr>
              <td>";
                    // line 12
                    echo twig_escape_filter($this->env, $context["item"], "html", null, true);
                    echo "</td>
              <td class=\"right\">
                <a class=\"unhideNavItem ajax\" href=\"navigation.php\" data-post=\"";
                    // line 14
                    echo PhpMyAdmin\Url::getCommon(["unhideNavItem" => true, "itemType" =>                     // line 16
$context["type"], "itemName" =>                     // line 17
$context["item"], "dbName" =>                     // line 18
($context["database"] ?? null)], "");
                    // line 19
                    echo "\">";
                    echo PhpMyAdmin\Util::getIcon("show", _gettext("Unhide"));
                    echo "</a>
              </td>
            </tr>
          ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['item'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 23
                echo "        </tbody>
      </table>
    ";
                ++$context['loop']['index0'];
                ++$context['loop']['index'];
                $context['loop']['first'] = false;
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['type'], $context['label'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 26
        echo "  </fieldset>
</form>
";
    }

    public function getTemplateName()
    {
        return "navigation/item_unhide_dialog.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  106 => 26,  94 => 23,  83 => 19,  81 => 18,  80 => 17,  79 => 16,  78 => 14,  73 => 12,  70 => 11,  66 => 10,  60 => 7,  55 => 6,  44 => 5,  39 => 3,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "navigation/item_unhide_dialog.twig", "/home/files/phpmyadmin/release/phpMyAdmin-5.0+snapshot/templates/navigation/item_unhide_dialog.twig");
    }
}

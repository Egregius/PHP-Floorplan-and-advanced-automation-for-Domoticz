<?php

/* prefs_twofactor.twig */
class __TwigTemplate_7fe3e2c32b1b425f882f84b976b08966ba89ac17289b6badfaea672fcf2f5d60 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        // line 1
        echo "<div class=\"group\">
<h2>
";
        // line 3
        echo _gettext("Two-factor authentication status");
        // line 4
        echo PhpMyAdmin\Util::showDocu("two_factor");
        echo "
</h2>
<div class=\"group-cnt\">
";
        // line 7
        if (($context["enabled"] ?? null)) {
            // line 8
            if ((($context["num_backends"] ?? null) == 0)) {
                // line 9
                echo "<p>";
                echo _gettext("Two-factor authentication is not available, please install optional dependencies to enable authentication backends.");
                echo "</p>
<p>";
                // line 10
                echo _gettext("Following composer packages are missing:");
                echo "</p>
<ul>
";
                // line 12
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable(($context["missing"] ?? null));
                foreach ($context['_seq'] as $context["_key"] => $context["item"]) {
                    // line 13
                    echo "    <li><code>";
                    echo twig_escape_filter($this->env, $this->getAttribute($context["item"], "dep", []), "html", null, true);
                    echo "</code> (";
                    echo twig_escape_filter($this->env, $this->getAttribute($context["item"], "class", []), "html", null, true);
                    echo ")</li>
";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['item'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 15
                echo "</ul>
";
            } else {
                // line 17
                if (($context["backend_id"] ?? null)) {
                    // line 18
                    echo "<p>";
                    echo _gettext("Two-factor authentication is available and configured for this account.");
                    echo "</p>
";
                } else {
                    // line 20
                    echo "<p>";
                    echo _gettext("Two-factor authentication is available, but not configured for this account.");
                    echo "</p>
";
                }
            }
        } else {
            // line 24
            echo "<p>";
            echo _gettext("Two-factor authentication is not available, enable phpMyAdmin configuration storage to use it.");
            echo "</p>
";
        }
        // line 26
        echo "</div>
</div>

";
        // line 29
        if (($context["backend_id"] ?? null)) {
            // line 30
            echo "<div class=\"group\">
<h2>";
            // line 31
            echo twig_escape_filter($this->env, ($context["backend_name"] ?? null), "html", null, true);
            echo "</h2>
<div class=\"group-cnt\">
<p>";
            // line 33
            echo _gettext("You have enabled two factor authentication.");
            echo "</p>
<p>";
            // line 34
            echo twig_escape_filter($this->env, ($context["backend_description"] ?? null), "html", null, true);
            echo "</p>
<form method=\"POST\" action=\"prefs_twofactor.php\">
";
            // line 36
            echo PhpMyAdmin\Url::getHiddenInputs();
            echo "
<input type=\"submit\" name=\"2fa_remove\" value=\"";
            // line 37
            echo _gettext("Disable two-factor authentication");
            echo "\" />
</form>
</div>
</div>
";
        } elseif ((        // line 41
($context["num_backends"] ?? null) > 0)) {
            // line 42
            echo "<div class=\"group\">
<h2>";
            // line 43
            echo _gettext("Configure two-factor authentication");
            echo "</h2>
<div class=\"group-cnt\">
<form method=\"POST\" action=\"prefs_twofactor.php\">
";
            // line 46
            echo PhpMyAdmin\Url::getHiddenInputs();
            echo "
";
            // line 47
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["backends"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["backend"]) {
                // line 48
                echo "<label>
<input type=\"radio\" name=\"2fa_configure\" ";
                // line 49
                if (($this->getAttribute($context["backend"], "id", [], "array") == "")) {
                    echo "checked=\"checked\"";
                }
                echo " value=\"";
                echo twig_escape_filter($this->env, $this->getAttribute($context["backend"], "id", [], "array"), "html", null, true);
                echo "\"/>
<strong>";
                // line 50
                echo twig_escape_filter($this->env, $this->getAttribute($context["backend"], "name", [], "array"), "html", null, true);
                echo "</strong>
<p>";
                // line 51
                echo twig_escape_filter($this->env, $this->getAttribute($context["backend"], "description", [], "array"), "html", null, true);
                echo "</p>
</label>
";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['backend'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 54
            echo "<input type=\"submit\" value=\"";
            echo _gettext("Configure two-factor authentication");
            echo "\" />
</form>
</div>
</div>
";
        }
    }

    public function getTemplateName()
    {
        return "prefs_twofactor.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  164 => 54,  155 => 51,  151 => 50,  143 => 49,  140 => 48,  136 => 47,  132 => 46,  126 => 43,  123 => 42,  121 => 41,  114 => 37,  110 => 36,  105 => 34,  101 => 33,  96 => 31,  93 => 30,  91 => 29,  86 => 26,  80 => 24,  72 => 20,  66 => 18,  64 => 17,  60 => 15,  49 => 13,  45 => 12,  40 => 10,  35 => 9,  33 => 8,  31 => 7,  25 => 4,  23 => 3,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "prefs_twofactor.twig", "/var/www/home.egregius.be/secure/phpMyAdmin/templates/prefs_twofactor.twig");
    }
}

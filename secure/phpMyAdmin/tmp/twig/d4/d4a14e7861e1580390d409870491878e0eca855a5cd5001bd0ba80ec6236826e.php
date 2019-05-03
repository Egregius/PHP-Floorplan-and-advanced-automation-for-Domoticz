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

/* header_location.twig */
class __TwigTemplate_3e70a9c335638bee56c05507407372c37002b8318b51d8e5171b7a3d0f71edb6 extends \Twig\Template
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
        // line 2
        echo "<html>
<head>
    <title>- - -</title>
    <meta http-equiv=\"expires\" content=\"0\">
    <meta http-equiv=\"Pragma\" content=\"no-cache\">
    <meta http-equiv=\"Cache-Control\" content=\"no-cache\">
    <meta http-equiv=\"Refresh\" content=\"0;url=";
        // line 8
        echo twig_escape_filter($this->env, ($context["uri"] ?? null), "html", null, true);
        echo "\">
    <script type=\"text/javascript\">
        //<![CDATA[
        setTimeout(function() { window.location = decodeURI('";
        // line 11
        echo PhpMyAdmin\Sanitize::escapeJsString(($context["uri"] ?? null));
        echo "'); }, 2000);
        //]]>
    </script>
</head>
<body>
<script type=\"text/javascript\">
    //<![CDATA[
    document.write('<p><a href=\"";
        // line 18
        echo PhpMyAdmin\Sanitize::escapeJsString(twig_escape_filter($this->env, ($context["uri"] ?? null)));
        echo "\">";
        echo _gettext("Go");
        echo "</a></p>');
    //]]>
</script>
</body>
</html>
";
    }

    public function getTemplateName()
    {
        return "header_location.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  59 => 18,  49 => 11,  43 => 8,  35 => 2,);
    }

    public function getSourceContext()
    {
        return new Source("", "header_location.twig", "/home/files/phpmyadmin/release/phpMyAdmin-5.0+snapshot/templates/header_location.twig");
    }
}

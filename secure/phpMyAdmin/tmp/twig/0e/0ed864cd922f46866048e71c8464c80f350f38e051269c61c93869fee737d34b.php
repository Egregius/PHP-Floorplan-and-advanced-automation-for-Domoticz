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

/* server/status/base.twig */
class __TwigTemplate_9d766695fad63da538433b2275bf7b2844c71bbe8bcb589ff6784a1fc73146a0 extends \Twig\Template
{
    private $source;

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
            'content' => [$this, 'block_content'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        // line 1
        echo "<div>
  <ul id=\"topmenu2\">
    <li>
      <a href=\"server_status.php";
        // line 4
        echo PhpMyAdmin\Url::getCommon();
        echo "\"";
        echo (((($context["active"] ?? null) == "status")) ? (" class=\"tabactive\"") : (""));
        echo ">
        ";
        // line 5
        echo _gettext("Server");
        // line 6
        echo "      </a>
    </li>
    <li>
      <a href=\"server_status_processes.php";
        // line 9
        echo PhpMyAdmin\Url::getCommon();
        echo "\"";
        echo (((($context["active"] ?? null) == "processes")) ? (" class=\"tabactive\"") : (""));
        echo ">
        ";
        // line 10
        echo _gettext("Processes");
        // line 11
        echo "      </a>
    </li>
    <li>
      <a href=\"server_status_queries.php";
        // line 14
        echo PhpMyAdmin\Url::getCommon();
        echo "\"";
        echo (((($context["active"] ?? null) == "queries")) ? (" class=\"tabactive\"") : (""));
        echo ">
        ";
        // line 15
        echo _gettext("Query statistics");
        // line 16
        echo "      </a>
    </li>
    <li>
      <a href=\"server_status_variables.php";
        // line 19
        echo PhpMyAdmin\Url::getCommon();
        echo "\"";
        echo (((($context["active"] ?? null) == "variables")) ? (" class=\"tabactive\"") : (""));
        echo ">
        ";
        // line 20
        echo _gettext("All status variables");
        // line 21
        echo "      </a>
    </li>
    <li>
      <a href=\"server_status_monitor.php";
        // line 24
        echo PhpMyAdmin\Url::getCommon();
        echo "\"";
        echo (((($context["active"] ?? null) == "monitor")) ? (" class=\"tabactive\"") : (""));
        echo ">
        ";
        // line 25
        echo _gettext("Monitor");
        // line 26
        echo "      </a>
    </li>
    <li>
      <a href=\"server_status_advisor.php";
        // line 29
        echo PhpMyAdmin\Url::getCommon();
        echo "\"";
        echo (((($context["active"] ?? null) == "advisor")) ? (" class=\"tabactive\"") : (""));
        echo ">
        ";
        // line 30
        echo _gettext("Advisor");
        // line 31
        echo "      </a>
    </li>
  </ul>
  <div class=\"clearfloat\"></div>

  <div>
    ";
        // line 37
        $this->displayBlock('content', $context, $blocks);
        // line 38
        echo "  </div>
</div>
";
    }

    // line 37
    public function block_content($context, array $blocks = [])
    {
    }

    public function getTemplateName()
    {
        return "server/status/base.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  130 => 37,  124 => 38,  122 => 37,  114 => 31,  112 => 30,  106 => 29,  101 => 26,  99 => 25,  93 => 24,  88 => 21,  86 => 20,  80 => 19,  75 => 16,  73 => 15,  67 => 14,  62 => 11,  60 => 10,  54 => 9,  49 => 6,  47 => 5,  41 => 4,  36 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "server/status/base.twig", "/var/www/home.egregius.be/secure/phpMyAdmin/templates/server/status/base.twig");
    }
}

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

/* columns_definitions/column_attributes.twig */
class __TwigTemplate_30ac9909b156b84dabacf5c321d2599f705b4755e06694bf13e72f411101a6e8 extends \Twig\Template
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
        $context["ci"] = 0;
        // line 3
        echo "
";
        // line 6
        $context["ci_offset"] =  -1;
        // line 7
        echo "
<td class=\"center\">
    ";
        // line 10
        echo "    ";
        $this->loadTemplate("columns_definitions/column_name.twig", "columns_definitions/column_attributes.twig", 10)->display(twig_to_array(["column_number" =>         // line 11
($context["column_number"] ?? null), "ci" =>         // line 12
($context["ci"] ?? null), "ci_offset" =>         // line 13
($context["ci_offset"] ?? null), "column_meta" =>         // line 14
($context["column_meta"] ?? null), "cfg_relation" =>         // line 15
($context["cfg_relation"] ?? null), "max_rows" =>         // line 16
($context["max_rows"] ?? null)]));
        // line 18
        echo "    ";
        $context["ci"] = (($context["ci"] ?? null) + 1);
        // line 19
        echo "</td>
<td class=\"center\">
    ";
        // line 22
        echo "    ";
        $this->loadTemplate("columns_definitions/column_type.twig", "columns_definitions/column_attributes.twig", 22)->display(twig_to_array(["column_number" =>         // line 23
($context["column_number"] ?? null), "ci" =>         // line 24
($context["ci"] ?? null), "ci_offset" =>         // line 25
($context["ci_offset"] ?? null), "column_meta" =>         // line 26
($context["column_meta"] ?? null), "type_upper" =>         // line 27
($context["type_upper"] ?? null)]));
        // line 29
        echo "    ";
        $context["ci"] = (($context["ci"] ?? null) + 1);
        // line 30
        echo "</td>
<td class=\"center\">
    ";
        // line 33
        echo "    ";
        $this->loadTemplate("columns_definitions/column_length.twig", "columns_definitions/column_attributes.twig", 33)->display(twig_to_array(["column_number" =>         // line 34
($context["column_number"] ?? null), "ci" =>         // line 35
($context["ci"] ?? null), "ci_offset" =>         // line 36
($context["ci_offset"] ?? null), "length_values_input_size" =>         // line 37
($context["length_values_input_size"] ?? null), "length_to_display" =>         // line 38
($context["length"] ?? null)]));
        // line 40
        echo "    ";
        $context["ci"] = (($context["ci"] ?? null) + 1);
        // line 41
        echo "</td>
<td class=\"center\">
    ";
        // line 44
        echo "    ";
        $this->loadTemplate("columns_definitions/column_default.twig", "columns_definitions/column_attributes.twig", 44)->display(twig_to_array(["column_number" =>         // line 45
($context["column_number"] ?? null), "ci" =>         // line 46
($context["ci"] ?? null), "ci_offset" =>         // line 47
($context["ci_offset"] ?? null), "column_meta" =>         // line 48
($context["column_meta"] ?? null), "type_upper" =>         // line 49
($context["type_upper"] ?? null), "default_value" =>         // line 50
($context["default_value"] ?? null), "char_editing" =>         // line 51
($context["char_editing"] ?? null)]));
        // line 53
        echo "    ";
        $context["ci"] = (($context["ci"] ?? null) + 1);
        // line 54
        echo "</td>
<td class=\"center\">
    ";
        // line 57
        echo "    ";
        echo PhpMyAdmin\Charsets::getCollationDropdownBox(        // line 58
($context["dbi"] ?? null),         // line 59
($context["disable_is"] ?? null), (("field_collation[" .         // line 60
($context["column_number"] ?? null)) . "]"), ((("field_" .         // line 61
($context["column_number"] ?? null)) . "_") . (($context["ci"] ?? null) - ($context["ci_offset"] ?? null))), (( !twig_test_empty((($__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4 =         // line 62
($context["column_meta"] ?? null)) && is_array($__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4) || $__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4 instanceof ArrayAccess ? ($__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4["Collation"] ?? null) : null))) ? ((($__internal_62824350bc4502ee19dbc2e99fc6bdd3bd90e7d8dd6e72f42c35efd048542144 = ($context["column_meta"] ?? null)) && is_array($__internal_62824350bc4502ee19dbc2e99fc6bdd3bd90e7d8dd6e72f42c35efd048542144) || $__internal_62824350bc4502ee19dbc2e99fc6bdd3bd90e7d8dd6e72f42c35efd048542144 instanceof ArrayAccess ? ($__internal_62824350bc4502ee19dbc2e99fc6bdd3bd90e7d8dd6e72f42c35efd048542144["Collation"] ?? null) : null)) : (null)), false);
        // line 64
        echo "
    ";
        // line 65
        $context["ci"] = (($context["ci"] ?? null) + 1);
        // line 66
        echo "</td>
<td class=\"center\">
    ";
        // line 69
        echo "    ";
        $this->loadTemplate("columns_definitions/column_attribute.twig", "columns_definitions/column_attributes.twig", 69)->display(twig_to_array(["column_number" =>         // line 70
($context["column_number"] ?? null), "ci" =>         // line 71
($context["ci"] ?? null), "ci_offset" =>         // line 72
($context["ci_offset"] ?? null), "column_meta" =>         // line 73
($context["column_meta"] ?? null), "extracted_columnspec" =>         // line 74
($context["extracted_columnspec"] ?? null), "submit_attribute" =>         // line 75
($context["submit_attribute"] ?? null), "attribute_types" =>         // line 76
($context["attribute_types"] ?? null)]));
        // line 78
        echo "    ";
        $context["ci"] = (($context["ci"] ?? null) + 1);
        // line 79
        echo "</td>
<td class=\"center\">
    ";
        // line 82
        echo "    ";
        $this->loadTemplate("columns_definitions/column_null.twig", "columns_definitions/column_attributes.twig", 82)->display(twig_to_array(["column_number" =>         // line 83
($context["column_number"] ?? null), "ci" =>         // line 84
($context["ci"] ?? null), "ci_offset" =>         // line 85
($context["ci_offset"] ?? null), "column_meta" =>         // line 86
($context["column_meta"] ?? null)]));
        // line 88
        echo "    ";
        $context["ci"] = (($context["ci"] ?? null) + 1);
        // line 89
        echo "</td>
";
        // line 90
        if (((isset($context["change_column"]) || array_key_exists("change_column", $context)) &&  !twig_test_empty(($context["change_column"] ?? null)))) {
            // line 91
            echo "    ";
            // line 92
            echo "    <td class=\"center\">
        ";
            // line 93
            $this->loadTemplate("columns_definitions/column_adjust_privileges.twig", "columns_definitions/column_attributes.twig", 93)->display(twig_to_array(["column_number" =>             // line 94
($context["column_number"] ?? null), "ci" =>             // line 95
($context["ci"] ?? null), "ci_offset" =>             // line 96
($context["ci_offset"] ?? null), "privs_available" =>             // line 97
($context["privs_available"] ?? null)]));
            // line 99
            echo "        ";
            $context["ci"] = (($context["ci"] ?? null) + 1);
            // line 100
            echo "    </td>
";
        }
        // line 102
        if ( !($context["is_backup"] ?? null)) {
            // line 103
            echo "    ";
            // line 104
            echo "    <td class=\"center\">
        ";
            // line 105
            $this->loadTemplate("columns_definitions/column_indexes.twig", "columns_definitions/column_attributes.twig", 105)->display(twig_to_array(["column_number" =>             // line 106
($context["column_number"] ?? null), "ci" =>             // line 107
($context["ci"] ?? null), "ci_offset" =>             // line 108
($context["ci_offset"] ?? null), "column_meta" =>             // line 109
($context["column_meta"] ?? null)]));
            // line 111
            echo "        ";
            $context["ci"] = (($context["ci"] ?? null) + 1);
            // line 112
            echo "    </td>
";
        }
        // line 114
        echo "<td class=\"center\">
    ";
        // line 116
        echo "    ";
        $this->loadTemplate("columns_definitions/column_auto_increment.twig", "columns_definitions/column_attributes.twig", 116)->display(twig_to_array(["column_number" =>         // line 117
($context["column_number"] ?? null), "ci" =>         // line 118
($context["ci"] ?? null), "ci_offset" =>         // line 119
($context["ci_offset"] ?? null), "column_meta" =>         // line 120
($context["column_meta"] ?? null)]));
        // line 122
        echo "    ";
        $context["ci"] = (($context["ci"] ?? null) + 1);
        // line 123
        echo "</td>
<td class=\"center\">
    ";
        // line 126
        echo "    ";
        $this->loadTemplate("columns_definitions/column_comment.twig", "columns_definitions/column_attributes.twig", 126)->display(twig_to_array(["column_number" =>         // line 127
($context["column_number"] ?? null), "ci" =>         // line 128
($context["ci"] ?? null), "ci_offset" =>         // line 129
($context["ci_offset"] ?? null), "max_length" =>         // line 130
($context["max_length"] ?? null), "value" => ((((twig_get_attribute($this->env, $this->source,         // line 131
($context["column_meta"] ?? null), "Field", [], "array", true, true, false, 131) && twig_test_iterable(        // line 132
($context["comments_map"] ?? null))) && twig_get_attribute($this->env, $this->source,         // line 133
($context["comments_map"] ?? null), (($__internal_1cfccaec8dd2e8578ccb026fbe7f2e7e29ac2ed5deb976639c5fc99a6ea8583b = ($context["column_meta"] ?? null)) && is_array($__internal_1cfccaec8dd2e8578ccb026fbe7f2e7e29ac2ed5deb976639c5fc99a6ea8583b) || $__internal_1cfccaec8dd2e8578ccb026fbe7f2e7e29ac2ed5deb976639c5fc99a6ea8583b instanceof ArrayAccess ? ($__internal_1cfccaec8dd2e8578ccb026fbe7f2e7e29ac2ed5deb976639c5fc99a6ea8583b["Field"] ?? null) : null), [], "array", true, true, false, 133))) ? (twig_escape_filter($this->env, (($__internal_68aa442c1d43d3410ea8f958ba9090f3eaa9a76f8de8fc9be4d6c7389ba28002 =         // line 134
($context["comments_map"] ?? null)) && is_array($__internal_68aa442c1d43d3410ea8f958ba9090f3eaa9a76f8de8fc9be4d6c7389ba28002) || $__internal_68aa442c1d43d3410ea8f958ba9090f3eaa9a76f8de8fc9be4d6c7389ba28002 instanceof ArrayAccess ? ($__internal_68aa442c1d43d3410ea8f958ba9090f3eaa9a76f8de8fc9be4d6c7389ba28002[(($__internal_d7fc55f1a54b629533d60b43063289db62e68921ee7a5f8de562bd9d4a2b7ad4 = ($context["column_meta"] ?? null)) && is_array($__internal_d7fc55f1a54b629533d60b43063289db62e68921ee7a5f8de562bd9d4a2b7ad4) || $__internal_d7fc55f1a54b629533d60b43063289db62e68921ee7a5f8de562bd9d4a2b7ad4 instanceof ArrayAccess ? ($__internal_d7fc55f1a54b629533d60b43063289db62e68921ee7a5f8de562bd9d4a2b7ad4["Field"] ?? null) : null)] ?? null) : null))) : (""))]));
        // line 136
        echo "    ";
        $context["ci"] = (($context["ci"] ?? null) + 1);
        // line 137
        echo "</td>
 ";
        // line 139
        if (($context["is_virtual_columns_supported"] ?? null)) {
            // line 140
            echo "    <td class=\"center\">
        ";
            // line 141
            $this->loadTemplate("columns_definitions/column_virtuality.twig", "columns_definitions/column_attributes.twig", 141)->display(twig_to_array(["column_number" =>             // line 142
($context["column_number"] ?? null), "ci" =>             // line 143
($context["ci"] ?? null), "ci_offset" =>             // line 144
($context["ci_offset"] ?? null), "column_meta" =>             // line 145
($context["column_meta"] ?? null), "char_editing" =>             // line 146
($context["char_editing"] ?? null), "expression" => ((twig_get_attribute($this->env, $this->source,             // line 147
($context["column_meta"] ?? null), "Expression", [], "array", true, true, false, 147)) ? ((($__internal_01476f8db28655ee4ee02ea2d17dd5a92599be76304f08cd8bc0e05aced30666 = ($context["column_meta"] ?? null)) && is_array($__internal_01476f8db28655ee4ee02ea2d17dd5a92599be76304f08cd8bc0e05aced30666) || $__internal_01476f8db28655ee4ee02ea2d17dd5a92599be76304f08cd8bc0e05aced30666 instanceof ArrayAccess ? ($__internal_01476f8db28655ee4ee02ea2d17dd5a92599be76304f08cd8bc0e05aced30666["Expression"] ?? null) : null)) : ("")), "options" =>             // line 148
($context["options"] ?? null)]));
            // line 150
            echo "        ";
            $context["ci"] = (($context["ci"] ?? null) + 1);
            // line 151
            echo "    </td>
";
        }
        // line 154
        if ((isset($context["fields_meta"]) || array_key_exists("fields_meta", $context))) {
            // line 155
            echo "    ";
            $context["current_index"] = 0;
            // line 156
            echo "    ";
            $context["cols"] = (twig_length_filter($this->env, ($context["move_columns"] ?? null)) - 1);
            // line 157
            echo "    ";
            $context["break"] = false;
            // line 158
            echo "    ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(range(0, ($context["cols"] ?? null)));
            foreach ($context['_seq'] as $context["_key"] => $context["mi"]) {
                if (((twig_get_attribute($this->env, $this->source, (($__internal_01c35b74bd85735098add188b3f8372ba465b232ab8298cb582c60f493d3c22e = ($context["move_columns"] ?? null)) && is_array($__internal_01c35b74bd85735098add188b3f8372ba465b232ab8298cb582c60f493d3c22e) || $__internal_01c35b74bd85735098add188b3f8372ba465b232ab8298cb582c60f493d3c22e instanceof ArrayAccess ? ($__internal_01c35b74bd85735098add188b3f8372ba465b232ab8298cb582c60f493d3c22e[$context["mi"]] ?? null) : null), "name", [], "any", false, false, false, 158) == (($__internal_63ad1f9a2bf4db4af64b010785e9665558fdcac0e8db8b5b413ed986c62dbb52 = ($context["column_meta"] ?? null)) && is_array($__internal_63ad1f9a2bf4db4af64b010785e9665558fdcac0e8db8b5b413ed986c62dbb52) || $__internal_63ad1f9a2bf4db4af64b010785e9665558fdcac0e8db8b5b413ed986c62dbb52 instanceof ArrayAccess ? ($__internal_63ad1f9a2bf4db4af64b010785e9665558fdcac0e8db8b5b413ed986c62dbb52["Field"] ?? null) : null)) &&  !($context["break"] ?? null))) {
                    // line 159
                    echo "        ";
                    $context["current_index"] = $context["mi"];
                    // line 160
                    echo "        ";
                    $context["break"] = true;
                    // line 161
                    echo "    ";
                }
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['mi'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 162
            echo "
    <td class=\"center\">
        ";
            // line 164
            $this->loadTemplate("columns_definitions/move_column.twig", "columns_definitions/column_attributes.twig", 164)->display(twig_to_array(["column_number" =>             // line 165
($context["column_number"] ?? null), "ci" =>             // line 166
($context["ci"] ?? null), "ci_offset" =>             // line 167
($context["ci_offset"] ?? null), "column_meta" =>             // line 168
($context["column_meta"] ?? null), "move_columns" =>             // line 169
($context["move_columns"] ?? null), "current_index" =>             // line 170
($context["current_index"] ?? null)]));
            // line 172
            echo "        ";
            $context["ci"] = (($context["ci"] ?? null) + 1);
            // line 173
            echo "    </td>
";
        }
        // line 175
        echo "
";
        // line 176
        if ((((($__internal_f10a4cc339617934220127f034125576ed229e948660ebac906a15846d52f136 = ($context["cfg_relation"] ?? null)) && is_array($__internal_f10a4cc339617934220127f034125576ed229e948660ebac906a15846d52f136) || $__internal_f10a4cc339617934220127f034125576ed229e948660ebac906a15846d52f136 instanceof ArrayAccess ? ($__internal_f10a4cc339617934220127f034125576ed229e948660ebac906a15846d52f136["mimework"] ?? null) : null) && ($context["browse_mime"] ?? null)) && (($__internal_887a873a4dc3cf8bd4f99c487b4c7727999c350cc3a772414714e49a195e4386 = ($context["cfg_relation"] ?? null)) && is_array($__internal_887a873a4dc3cf8bd4f99c487b4c7727999c350cc3a772414714e49a195e4386) || $__internal_887a873a4dc3cf8bd4f99c487b4c7727999c350cc3a772414714e49a195e4386 instanceof ArrayAccess ? ($__internal_887a873a4dc3cf8bd4f99c487b4c7727999c350cc3a772414714e49a195e4386["commwork"] ?? null) : null))) {
            // line 177
            echo "    <td class=\"center\">
        ";
            // line 179
            echo "        ";
            $this->loadTemplate("columns_definitions/mime_type.twig", "columns_definitions/column_attributes.twig", 179)->display(twig_to_array(["column_number" =>             // line 180
($context["column_number"] ?? null), "ci" =>             // line 181
($context["ci"] ?? null), "ci_offset" =>             // line 182
($context["ci_offset"] ?? null), "column_meta" =>             // line 183
($context["column_meta"] ?? null), "available_mime" =>             // line 184
($context["available_mime"] ?? null), "mime_map" =>             // line 185
($context["mime_map"] ?? null)]));
            // line 187
            echo "        ";
            $context["ci"] = (($context["ci"] ?? null) + 1);
            // line 188
            echo "    </td>
    <td class=\"center\">
        ";
            // line 191
            echo "        ";
            $this->loadTemplate("columns_definitions/transformation.twig", "columns_definitions/column_attributes.twig", 191)->display(twig_to_array(["column_number" =>             // line 192
($context["column_number"] ?? null), "ci" =>             // line 193
($context["ci"] ?? null), "ci_offset" =>             // line 194
($context["ci_offset"] ?? null), "column_meta" =>             // line 195
($context["column_meta"] ?? null), "available_mime" =>             // line 196
($context["available_mime"] ?? null), "mime_map" =>             // line 197
($context["mime_map"] ?? null), "type" => "transformation"]));
            // line 200
            echo "        ";
            $context["ci"] = (($context["ci"] ?? null) + 1);
            // line 201
            echo "    </td>
    <td class=\"center\">
        ";
            // line 204
            echo "        ";
            $this->loadTemplate("columns_definitions/transformation_option.twig", "columns_definitions/column_attributes.twig", 204)->display(twig_to_array(["column_number" =>             // line 205
($context["column_number"] ?? null), "ci" =>             // line 206
($context["ci"] ?? null), "ci_offset" =>             // line 207
($context["ci_offset"] ?? null), "column_meta" =>             // line 208
($context["column_meta"] ?? null), "mime_map" =>             // line 209
($context["mime_map"] ?? null), "type_prefix" => ""]));
            // line 212
            echo "        ";
            $context["ci"] = (($context["ci"] ?? null) + 1);
            // line 213
            echo "    </td>
    <td class=\"center\">
        ";
            // line 216
            echo "        ";
            $this->loadTemplate("columns_definitions/transformation.twig", "columns_definitions/column_attributes.twig", 216)->display(twig_to_array(["column_number" =>             // line 217
($context["column_number"] ?? null), "ci" =>             // line 218
($context["ci"] ?? null), "ci_offset" =>             // line 219
($context["ci_offset"] ?? null), "column_meta" =>             // line 220
($context["column_meta"] ?? null), "available_mime" =>             // line 221
($context["available_mime"] ?? null), "mime_map" =>             // line 222
($context["mime_map"] ?? null), "type" => "input_transformation"]));
            // line 225
            echo "        ";
            $context["ci"] = (($context["ci"] ?? null) + 1);
            // line 226
            echo "    </td>
    <td class=\"center\">
        ";
            // line 229
            echo "        ";
            $this->loadTemplate("columns_definitions/transformation_option.twig", "columns_definitions/column_attributes.twig", 229)->display(twig_to_array(["column_number" =>             // line 230
($context["column_number"] ?? null), "ci" =>             // line 231
($context["ci"] ?? null), "ci_offset" =>             // line 232
($context["ci_offset"] ?? null), "column_meta" =>             // line 233
($context["column_meta"] ?? null), "mime_map" =>             // line 234
($context["mime_map"] ?? null), "type_prefix" => "input_"]));
            // line 237
            echo "        ";
            $context["ci"] = (($context["ci"] ?? null) + 1);
            // line 238
            echo "    </td>
";
        }
    }

    public function getTemplateName()
    {
        return "columns_definitions/column_attributes.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  382 => 238,  379 => 237,  377 => 234,  376 => 233,  375 => 232,  374 => 231,  373 => 230,  371 => 229,  367 => 226,  364 => 225,  362 => 222,  361 => 221,  360 => 220,  359 => 219,  358 => 218,  357 => 217,  355 => 216,  351 => 213,  348 => 212,  346 => 209,  345 => 208,  344 => 207,  343 => 206,  342 => 205,  340 => 204,  336 => 201,  333 => 200,  331 => 197,  330 => 196,  329 => 195,  328 => 194,  327 => 193,  326 => 192,  324 => 191,  320 => 188,  317 => 187,  315 => 185,  314 => 184,  313 => 183,  312 => 182,  311 => 181,  310 => 180,  308 => 179,  305 => 177,  303 => 176,  300 => 175,  296 => 173,  293 => 172,  291 => 170,  290 => 169,  289 => 168,  288 => 167,  287 => 166,  286 => 165,  285 => 164,  281 => 162,  274 => 161,  271 => 160,  268 => 159,  262 => 158,  259 => 157,  256 => 156,  253 => 155,  251 => 154,  247 => 151,  244 => 150,  242 => 148,  241 => 147,  240 => 146,  239 => 145,  238 => 144,  237 => 143,  236 => 142,  235 => 141,  232 => 140,  230 => 139,  227 => 137,  224 => 136,  222 => 134,  221 => 133,  220 => 132,  219 => 131,  218 => 130,  217 => 129,  216 => 128,  215 => 127,  213 => 126,  209 => 123,  206 => 122,  204 => 120,  203 => 119,  202 => 118,  201 => 117,  199 => 116,  196 => 114,  192 => 112,  189 => 111,  187 => 109,  186 => 108,  185 => 107,  184 => 106,  183 => 105,  180 => 104,  178 => 103,  176 => 102,  172 => 100,  169 => 99,  167 => 97,  166 => 96,  165 => 95,  164 => 94,  163 => 93,  160 => 92,  158 => 91,  156 => 90,  153 => 89,  150 => 88,  148 => 86,  147 => 85,  146 => 84,  145 => 83,  143 => 82,  139 => 79,  136 => 78,  134 => 76,  133 => 75,  132 => 74,  131 => 73,  130 => 72,  129 => 71,  128 => 70,  126 => 69,  122 => 66,  120 => 65,  117 => 64,  115 => 62,  114 => 61,  113 => 60,  112 => 59,  111 => 58,  109 => 57,  105 => 54,  102 => 53,  100 => 51,  99 => 50,  98 => 49,  97 => 48,  96 => 47,  95 => 46,  94 => 45,  92 => 44,  88 => 41,  85 => 40,  83 => 38,  82 => 37,  81 => 36,  80 => 35,  79 => 34,  77 => 33,  73 => 30,  70 => 29,  68 => 27,  67 => 26,  66 => 25,  65 => 24,  64 => 23,  62 => 22,  58 => 19,  55 => 18,  53 => 16,  52 => 15,  51 => 14,  50 => 13,  49 => 12,  48 => 11,  46 => 10,  42 => 7,  40 => 6,  37 => 3,  35 => 2,);
    }

    public function getSourceContext()
    {
        return new Source("", "columns_definitions/column_attributes.twig", "/home/files/phpmyadmin/release/phpMyAdmin-5.0+snapshot/templates/columns_definitions/column_attributes.twig");
    }
}

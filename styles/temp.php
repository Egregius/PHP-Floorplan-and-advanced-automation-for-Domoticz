<?php
include "general.php";
$css="
h2{font-size:36px;}

.fix{position:absolute;}
.center{text-align:center;}
.z1{z-index:100;}
.i48{width:55px;height:auto;}
.box{left:0px;width:80px;background:#222;padding-top:10px; border:thin #666}
input[type=checkbox]{position:absolute;left:-9999px;}
label{margin:0px;padding:14px 3px 14px 3px;border:0px solid #fff;color:#fff;background-color:#555;cursor:pointer;user-select:none;text-align:center}
input:checked + label{background-color:#ffba00;color:#000;}

.kamer{display: inline-block;width:148px;max-width:19.9%}
.uur{display: inline-block;width:22.6px;max-width:4,166666667%}
.even{background:#333;}
.borderleft1{border-left:1px solid #AAA;}
.borderleft2{border-left:1px solid #999;}
.borderright1{border-left:1px solid #AAA;}
.borderright2{border-left:1px solid #999;}
";
if ($udevice=='other') {
    $css.="
.datum{width:185px;height:36px;}
";
} elseif ($udevice=='iPad') {
    $css.="
@media only screen and (orientation: portrait) {
.datum{width:151px;height:28px;}
.btn{height:28px;font-size:1em;}
}
@media only screen and (orientation: landscape) {

}
";
} elseif ($udevice=='iPhone') {
    $css.="
@media only screen and (orientation: portrait) {
.datum{width:151px;height:28px;}
.btn{height:28px;font-size:1em;}
}
@media only screen and (orientation: landscape) {
.datum{width:170px;height:36px;}
}
";
}
$css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
$css = str_replace(': ', ':', $css);
$css = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $css);
echo($css);
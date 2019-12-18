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
label{margin:1px;padding:14px 3px 14px 3px;border:0px solid #fff;border-radius:14px;color:#fff;background-color:#555;cursor:pointer;user-select:none;}
input:checked + label{background-color:#4688F1;color:#000;}


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
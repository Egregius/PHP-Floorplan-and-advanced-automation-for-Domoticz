<?php
include "general.php";
$css="

html{padding:0px;margin:0px;color:#ccc;font-family:sans-serif;}
body{margin:0px;background:#000;width:100%}
body{margin:0 auto;}form,table{display:inline;margin:0px;padding:0px;}
a:link{text-decoration:none;color:#ccc}
a:visited{text-decoration:none;color:#ccc}
a:hover{text-decoration:none;color:#ccc;}
a:active{text-decoration:none;color:#ccc}
h2{font-size:36px;}
input[type=text]{cursor:pointer;-webkit-appearance:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;border-radius:0;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box;}
input[type=submit]{cursor:pointer;-webkit-appearance:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;border-radius:0;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box;}
input[type=date]{cursor:pointer;-webkit-appearance:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;border-radius:0;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box;}
input[type=select]{cursor:pointer;-webkit-appearance:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;border-radius:0;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box;}

.navbar{position:fixed;top:0px;left:0px;width:100%;height:80px;overflow:hidden;padding:2px 0px 2px 0px;z-index:100;background-color:#111;}

.btn{color:#ccc;background-color:#333;min-width:1.3em;text-align:center;display:inline-block;margin-bottom:0;cursor:pointer;border:1px solid transparent;padding:2px;margin:2px 0px 2px 2px;font-size:1em;-webkit-appearance:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;}
.btn:hover{color:#fff;}

/*input[type="text"]{font-size: 22px;padding-left:10px;}
input[type=checkbox]{visibility:hidden;}
input[type=submit]{color:#ccc;background-color:#555;display:inline-block;margin-bottom:0;cursor:pointer;padding:1px;margin:2px 0px -2px 2px;font-size:18px;-webkit-appearance:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;height:80px;font-size:28px;}
*/
.fix{position:absolute;}
.center{text-align:center;}
.z{z-index:10;}
.z0{z-index:-100;}
.z1{z-index:100;}
.r0{transform:rotate(0deg);-webkit-transform:rotate(0deg);}
.r90{transform:rotate(90deg);-webkit-transform:rotate(90deg);}
.r270{transform:rotate(270deg);-webkit-transform:rotate(270deg);}
.i48{width:48px;height:auto;}
.i60{width:60px;height:auto;}
.i70{width:70px;height:auto;}
.i90{width:90px;height:auto;}
.box{left:0px;width:80px;background:#222;padding-top:10px; border:thin #666}

.header{top:0px;left:0px;margin-top:0px;width:100%;}
/* GENERAL */
html{padding:0px;margin:0px;color:#ccc;}
body{background:#000;color:#999;}
body{margin:0 auto;}form,table{display:inline;margin:0px;padding:0px;}
a:link{text-decoration:none;color:#ccc}
a:visited{text-decoration:none;color:#ccc}
a:hover{text-decoration:none;color:#ccc;}
a:active{text-decoration:none;color:#ccc}
h2{font-size:36px;}
input[type=checkbox]{visibility:hidden;}
input[type=submit]{color:#ccc;background-color:#555;display:inline-block;margin-bottom:0;cursor:pointer;border:1px solid transparent;padding:1px;margin:2px 0px -2px 2px;font-size:18px;-webkit-appearance:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;height:80px;font-size:28px;margin:4px;}
.fix{position:absolute;}
.center{text-align:center;}
.z{z-index:10;}
.z0{z-index:-100;}
.z1{z-index:100;}
.r0{transform:rotate(0deg);-webkit-transform:rotate(0deg);}
.r90{transform:rotate(90deg);-webkit-transform:rotate(90deg);}
.r270{transform:rotate(270deg);-webkit-transform:rotate(270deg);}
.i48{width:48px;height:auto;}
.i60{width:60px;height:auto;}
.i70{width:70px;height:auto;}
.i90{width:90px;height:auto;}

/* CAMERA's */
.camera{top:100px;left:0px;width:100%;}
.camera1{top:100px;left:0px;width:50%;}
.camera2{top:100px;left:50%;width:50%;}
";
if ($udevice=='other') {
    $css.="
.camerai{width:auto;height:960px;}
";
} elseif ($udevice=='iPhone') {
    $css.="
.camerai{width:auto;height:960px;}
";
} elseif ($udevice=='iPad') {
    $css="
@media only screen and (orientation: portrait) {
    .camerai{width:100%;height:auto;}
}
@media only screen and (orientation: landscape) {
    .camerai{width:100%;height:auto;}
}
";
}
$css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
$css = str_replace(': ', ':', $css);
$css = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $css);
echo($css);

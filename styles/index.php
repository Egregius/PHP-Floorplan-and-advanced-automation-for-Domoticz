<?php
include "general.php";
$css="
body{font-family: sans-serif;}
.grid{}
.grid:after{content:''; display:block;clear:both;}
.gutter-sizer{width:4px;}
.grid-item{float:left;background-color:rgb(0,0,0);background-color:rgba(10,10,10,0.5);border-color:hsla(0,0%,0%,0.5);}
h2{font-size:36px;}
h2{font-size:16px; font-weight:800;padding:5px 0px 5px 0px; margin:0px; text-align:center;}
.btn{background-color:#333;color:#ccc;text-align:center;display:inline-block;border:0px solid transparent;padding:0px;margin:1px !important;-webkit-appearance:none;white-space:nowrap;overflow:hidden;}
.b1{width:1500px;max-width:96%;padding:8px 0px !important;}
.b2{width:1500px;max-width:47%;padding:8px 0px !important;}
.b3{width:1500px;max-width:30.5%;padding:8px 0px !important;}
.b4{width:1500px;max-width:22%;padding:8px 0px !important;}
";
if ($udevice=='other') {
    $css.="
body{background-image:url('/images/_firework.jpg');background-size:contain;background-repeat: no-repeat;background-attachment: fixed;background-position: center bottom; }
.grid-sizer,
.grid-item{width:19.3%;padding:0px 2px 2px 2px;}
.btn{height:20px;font-size:1.1em;}
.home{height:19px;font-size:1.1em;border:1px solid #800;}
.logout{position:absolute;bottom:0px;right:0px;}
";
} elseif ($udevice=='iPhone') {
    $css.="
.grid-sizer,
.grid-item{width:99.5%;padding:0px 2px 2px 2px;}
.btn{height:24px;font-size:1.4em;}
.home{height:23px;font-size:1.4em;border:1px solid #800;}
.logout{position:absolute;bottom:0px;right:0px;}
";
} elseif ($udevice=='iPhoneSE') {
    $css.="
.grid-sizer,
.grid-item{width:99.5%;padding:0px 2px 2px 2px;}
.btn{height:24px;font-size:1.4em;}
.home{height:23px;font-size:1.4em;border:1px solid #800;}
.logout{position:absolute;bottom:0px;right:0px;}
";
} elseif ($udevice=='iPad') {
    $css.="
.btn{height:30px;font-size:1.4em;}
.home{height:30px;box-shadow:0px 0px 0px 1px #700 inset;}
.logout{position:absolute;bottom:0px;right:0px;}
@media only screen and (orientation: portrait) {
.grid-sizer,
.grid-item{width:48.5%;padding:0px 2px 2px 2px;margin-bottom: 10px;}
}
@media only screen and (orientation: landscape) {
.grid-sizer,
.grid-item{width:32.5%;padding:0px 2px 2px 2px;margin-bottom: 10px;}
}
";
}
$css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
$css = str_replace(': ', ':', $css);
$css = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $css);
echo($css);
<?php include "general.php";?>

h2{font-size:24px;}

.fix{position:absolute;}
.center{text-align:center;}
.z1{z-index:100;}
.i48{width:55px;height:auto;}
.box{left:0px;width:80px;background:#222;padding-top:10px; border:thin #666}
.menu{position:fixed;top:0px;left:0px;}
td{overflow:hidden;white-space:nowrap;}
<?php if($udevice=='other'){ ?>
.btn{height:22px;font-size:1em;}
table{width:900px;margin:0 auto;table-layout:fixed;}
<?php }elseif($udevice=='iPad'){ ?>
@media only screen and (orientation: portrait) {

}
@media only screen and (orientation: landscape) {

}
<?php } ?>

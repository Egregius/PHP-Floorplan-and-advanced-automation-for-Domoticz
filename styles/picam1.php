<?php include "general.php";?>
.fix{position:absolute;}
input[type=select]{cursor:pointer;-webkit-appearance:none;border-radius:0;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box;border:0px solid transparent;text-align:center;}

<?php if($udevice=='other'){ ?>
input[type=submit]{	color:#ccc;background-color:#555;display:inline-block;cursor:pointer;border:0px solid transparent;-webkit-appearance:none;-webkit-user-select:none;	-moz-user-select:none;-ms-user-select:none;user-select:none;}
.menu{top:0px;left:0px;width:90%;height:40px;}
.camera1{top:80px;left:0px;width:50%;height:100%;}
.camera2{top:80px;left:50%;width:50%;height:100%;}
.camerai{width:100%;height:auto;}

<?php }elseif($udevice=='iPhone'){ ?>
@media only screen and (orientation: portrait) {
	input[type=submit]{	color:#ccc;background-color:#555;display:inline-block;cursor:pointer;border:0px solid transparent;padding:2px;-webkit-appearance:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;}
	.menu{top:0px;left:0px;width:100%;height:40px;z-index:10;}
	.camera1{top:100px;left:0px;width:100%;height:100%;}
	.camera2{top:600px;left:0px;width:100%;height:100%;}
	.camerai{width:100%;height:auto;}
	.btn{height:80px;font-size:1.2em;}
	.b6{width:101px;max-width:unset;}
	.b7{width:102px;max-width:unset;}
}
@media only screen and (orientation: landscape) {
	.fix{position:relative;}
	input[type=submit]{color:#ccc;background-color:#555;display:inline-block;cursor:pointer;border:0px solid transparent;padding:2px;-webkit-appearance:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;;}
	.menu{top:0px;left:0px;width:100%;height:40px;z-index:10;}
	.camera1{top:80px;left:0px;width:100%;height:100%;}
	.camera2{top:340px;left:0px;width:100%;height:100%;}
	.camerai{width:100%;height:auto;}
	.btn{height:60px;font-size:2em;}
	.b6{width:165px;max-width:unset;}
	.b7{width:187px;max-width:unset;}}

<?php }elseif($udevice=='iPad'){ ?>
@media only screen and (orientation: portrait) {
	.navbar{position:fixed;top:-5px;left:0px;width:100%;padding:0px;z-index:100;}
	input[type=submit]{	color:#ccc;background-color:#555;display:inline-block;cursor:pointer;border:1px solid transparent;padding:5px;margin:3px 0px -2px 4px;-webkit-appearance:none;	-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;}
	.menu{top:0px;left:0px;width:90%;height:0px;}
	.camera1{top:50px;left:50px;width:1314px;height:auto;z-index:-10;}
	.camera2{top:1030px;left:50px;width:1320px;height:auto;z-index:-11;}
	.camerai{width:100%;height:auto;}
	.btn{height:60px;font-size:2em;}
	.b6{width:229px;max-width:unset;}
	.b7{width:252px;max-width:unset;}
}
@media only screen and (orientation: landscape) {
	.navbar{position:fixed;top:5px;left:1060px;width:1000px;padding:0px;z-index:100;}
	input[type=submit]{color:#ccc;background-color:#555;display:inline-block;cursor:pointer;border:1px solid transparent;padding:5px;margin:3px 0px -2px 4px;-webkit-appearance:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;}
	.menu{top:100px;left:1200px;width:90%;height:40px;}
	.camera1{top:0px;left:0px;width:1060px;height:auto;z-index:-10;}
	.camera2{top:695px;right:0px;width:1060px;height:auto;z-index:-11;}
	.camerai{width:100%;height:auto;}
	.btn{height:120px;font-size:3em;color:#ccc;background-color:#555;}
	.b6{width:320px;max-width:unset;}
	.b7{width:320px;max-width:unset;}
}
<?php } ?>

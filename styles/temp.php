<?php include "general.php";?>

h2{font-size:36px;}

.fix{position:absolute;}
.center{text-align:center;}
.z1{z-index:100;}
.i48{width:55px;height:auto;}
.box{left:0px;width:80px;background:#222;padding-top:10px; border:thin #666}

<?php if($udevice=='other'){ ?>
.datum{width:185px;height:36px;}
<?php }elseif($udevice=='iPad'){ ?>
@media only screen and (orientation: portrait) {
.datum{width:151px;height:28px;}
.btn{height:28px;font-size:1em;}
}
@media only screen and (orientation: landscape) {

}
<?php }elseif($udevice=='iPhone'){ ?>
@media only screen and (orientation: portrait) {
.datum{width:151px;height:28px;}
.btn{height:28px;font-size:1em;}
}
@media only screen and (orientation: landscape) {
.datum{width:170px;height:36px;}
}
<?php } ?>

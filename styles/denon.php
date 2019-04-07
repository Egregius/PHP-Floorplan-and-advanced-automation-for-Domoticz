<?php include "general.php";?>
.isotope:after{content:'';display:block;clear:both;}
.grid{position:relative;clear:both;width:calc(100%-20px);}
.grid:after {content: '';display: block;clear: both;}

.box{text-align:center;left:0px;background:#222;padding:5px;margin:10px;}
.right{text-align:right;}

<?php if($udevice=='other'){ ?>
    h1{font-size:2em;}
    .menu{width:20.5%;max-width:300px;}
    .volume{width:14.9%;max-width:96px;}
    .delay{width:14.9%;max-width:30px;}
    .level{width:11.3%;max-width:105px;}
    .surround{width:45%;max-width:300px;}

<?php }elseif($udevice=='iPhone'){ ?>
    h1{font-size:2em;}
    .b3{width:240px;height:64px!important;}
    .b4{width:240px;height:60px!important;}
    .b5{width:200px;height:60px!important;}
    .menu{width:139px;height:60px!important;}
    .volume{width:66px;height:60px!important;}
    .delay{width:80px;height:60px!important;}
    .level{width:64px;height:60px!important;}
    .surround{width:120px;height:60px!important;}
    .btn{height:140px;font-size:2.5em!important}

<?php }elseif($udevice=='iPad'){ ?>
@media only screen and (orientation: portrait) {
    h1{font-size:4em;}
    .b3{width:240px;height:64px!important;}
    .b4{width:240px;height:60px!important;}
    .menu{width:362px;}
    .volume{width:123px;}
    .delay{width:82px;height:50px!important;}
    .level{width:98px;}
    .surround{width:480px;}
    .btn{height:140px;font-size: 2em}
}
@media only screen and (orientation: landscape) {
    h1{font-size:5em;}
    .menu{width:490px;}
    .volume{width:192px;}
    .delay{width:192px;}
    .level{width:135px;}
    .surround{width:494px;}
    .btn{height:120px;}
}
<?php } ?>

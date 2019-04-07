<?php include "general.php";?>
input[type=submit]{cursor:pointer;-webkit-appearance:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;border-radius:0;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box;}
.clear{clear:both;}.isotope:after{content:'';display:block;clear:both;}
.grid{position:relative;clear:both;width:calc(100%-20px);}
.grid:after {content: '';display: block;clear: both;}
form{display:inline;margin:0px;padding:0px;}

<?php if($udevice=='other'){ ?>
h1{font-size:2.2em;margin:5px;padding:0px;}
h2{font-size:1.4em;margin:0px;padding:0px;}
.navbar{position: fixed;top:0px;left:0px;min-height:38px;width:100%;padding:0px;z-index:10;background-color:#111;}
.isotope{height:100%;min-height:80%;margin:0 auto;padding-top:50px;}
.btn{height:45px;font-size:1.4em;}
.big{min-height:87px;}
.volume{width:14.9%;max-width:96px;}
.active{background-color:#ffba00;color:#000;}
.active:hover{background-color:#ffba00;color:#000;}
.input{width:23.3%;max-width:300px;}
.level{width:11.3%;max-width:105px;}
.surround{width:45%;max-width:300px;}

<?php }elseif($udevice=='iPhone'){ ?>
h1{font-size:2.5em;margin:0px;padding:0px;}
h2{font-size:1.8em;margin:0px;padding:0px;}
.navbar{position: fixed;top:0px;left:0px;float:left;min-height:38px;width:100%;padding:0px;z-index:10;background-color:#111;}
.isotope{height:100%;min-height:80%;margin:0 auto;padding-top:95px;}
.btn{height:85px;font-size:1.5em;padding:17px;}
.big{min-height:118px;}
.volume{width:112px;}
.active{background-color:#ffba00;color:#000;}
.active:hover{background-color:#ffba00;color:#000;}
.input{width:142px;}
.level{width:64px;}
.surround{width:120px;}

<?php }elseif($udevice=='iPad'){ ?>
@media only screen and (orientation: portrait) {
	h1{font-size:4em;margin:5px;padding:0px;}
	h2{font-size:2em;margin:0px;padding:0px;}
	.navbar{position:fixed;top:0px;left:0px;float:left;min-height:38px;width:100%;padding:0px;z-index:10;background-color:#111;}
	.isotope{height:100%;min-height:80%;margin:0 auto;padding-top:130px;}
	.btn{height:124px;font-size:3em;}
	.big{min-height:100px;}
	.volume{width:240px;}
	.active{background-color:#ffba00;color:#000;}
	.active:hover{background-color:#ffba00;color:#000;}
	.input{width:370px;}
	.level{width:98px;}
	.surround{width:480px;}
	.title{min-height:250px;}
	.controls{min-height:250px;}
	.audios{min-height:150px;}
	.subs{min-height:250px;}
}

@media only screen and (orientation: landscape) {
	h1{font-size:4em;margin:5px;padding:0px;}
	h2{font-size:3em;margin:5px;padding:0px;}
	.navbar{position: fixed;top:0px;left:0px;float:left;min-height:38px;width:100%;padding:0px;z-index:10;background-color:#111;}
	.isotope{height:100%;min-height:80%;margin:0 auto;padding-top:115px;}
	.btn{height:106px;font-size:3.5em;margin:4px;padding:0px 5px 0px 5px;}
	.big{min-height:100px;}
	.volume{width:192px;}
	.active{background-color:#ffba00;color:#000;}
	.active:hover{background-color:#ffba00;color:#000;}
	.input{width:496px;}
	.level{width:135px;}
	.surround{width:494px;}
}
<?php } ?>

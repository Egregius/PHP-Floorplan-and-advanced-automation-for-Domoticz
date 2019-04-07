<?php include "general.php";?>

<?php if($udevice=='other'){ ?>
input[type=submit]{color:#ccc;background-color:#444;display:inline-block;cursor:pointer;border:0px solid transparent;padding:3px;margin:3px 0px -2px 4px;-webkit-appearance:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;font-size:28px;}
.menu{top:0px;left:0px;width:90%;height:40px;}
.marginright{margin-right:0px;}
.thumb{display:inline;margin:0px;padding:2px 0px 1px 1px;vertical-align:top;font-size:0.88em;border:0px solid transparent;}
.thumbimage{padding:0px;width:240px;height:auto;}
<?php }elseif($udevice=='iPhone'){ ?>
  input[type=submit]{color:#ccc;background-color:#444;display:inline-block;cursor:pointer;border:0px solid transparent;padding:0px;margin:-2px;-webkit-appearance:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;font-size:16px;width:40px;height:60px;}
  .menu{top:0px;left:0px;width:100%;height:40px;}
  .marginright{margin-right:0px;}
  .thumb{display:inline;margin:1px;padding:2px 0px 1px 1px;vertical-align:top;font-size:0.8em;border:0px solid transparent;}
  .thumbimage{padding:0px;width:100px;height:auto;}

<?php }elseif($udevice=='iPad'){ ?>
@media only screen and (orientation: portrait) {
  input[type=submit]{color:#ccc;background-color:#444;display:inline-block;cursor:pointer;border:0px solid transparent;padding:3px;margin:3px 0px -2px 4px;-webkit-appearance:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;font-size:28px;width:154px;height:60px;}
  .menu{top:0px;left:0px;width:100%;height:40px;}
  .marginright{margin-right:120px;}
  .thumb{display:inline;margin:1px;padding:2px 0px 1px 1px;vertical-align:top;font-size:0.88em;border:0px solid transparent;}
  .thumbimage{padding:0px;width:123px;height:auto;}

}
@media only screen and (orientation: landscape) {
  input[type=submit]{color:#ccc;background-color:#444;display:inline-block;cursor:pointer;border:0px solid transparent;padding:3px;margin:3px 0px -2px 4px;-webkit-appearance:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;font-size:28px;width:210px;height:60px;}
  .menu{top:0px;left:0px;width:100%;height:40px;}
  .marginright{margin-right:120px;}
  .thumb{display:inline;margin:1px;padding:2px 0px 1px 1px;vertical-align:top;font-size:0.88em;border:0px solid transparent;}
  .thumbimage{padding:0px;width:166px;height:auto;}

}
<?php } ?>

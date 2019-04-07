<?php include "general.php";?>


h2{font-size:32px;padding: 0px;margin:0px;}
td{padding:1px 8px 2px 2px;}
input[type=submit]{cursor:pointer;-webkit-appearance:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;border-radius:0;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box;}
input[type=text]{cursor:pointer;-webkit-appearance:none;border-radius:0;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box;border:0px solid transparent;}
input[type=date]{cursor:pointer;-webkit-appearance:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;border-radius:0;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box;border:0px solid transparent;}
input[type=number]{cursor:pointer;-webkit-appearance:none;border-radius:0;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box;border:0px solid transparent;}
input[type=select]{cursor:pointer;-webkit-appearance:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;border-radius:0;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box;border:0px solid transparent;}
.delete{height:20px;padding:3px;box-sizing: border-box;-webkit-appearance:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;background:#666;color: #eee;outline: none;}
.menu{color:#ccc;background-color:#333;font-size:3em;text-align:center;vertical-align:middle;display:inline-block;cursor:pointer;border:1px solid transparent;padding:5px;margin:3px auto;}
.bottom{margin-top:5em;width:100%;}
.btn{color:#ccc;background-color:#333;width:97%;font-size:1.6em;
  text-align:center;
  vertical-align:middle;
  display:inline-block;
  cursor:pointer;
  border:1px solid transparent;
  padding:5px;
  margin:3px auto;
}
<?php if($udevice=='other'){ ?>
  .edit{width:100px;height:20px;padding:3px;box-sizing: border-box;-webkit-appearance:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;background:#666;color: #eee;outline: none;border:0px solid transparent;}

<?php }elseif($udevice=='iPad'){ ?>
@media only screen and (orientation: portrait) {
  .edit{width:100px;height:20px;padding:3px;box-sizing: border-box;-webkit-appearance:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;background:#666;color: #eee;outline: none;border:0px solid transparent;}

}
@media only screen and (orientation: landscape) {
  .edit{width:100px;height:20px;padding:3px;box-sizing: border-box;-webkit-appearance:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;background:#666;color: #eee;outline: none;border:0px solid transparent;}

}
<?php } ?>

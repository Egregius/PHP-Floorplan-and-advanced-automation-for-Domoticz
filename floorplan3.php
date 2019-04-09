<?php
/**
 * Pass2PHP
 * php version 7.3.3-1
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
if (isset($_REQUEST['autogetnzbs'])) {
    @file_get_contents('http://192.168.2.10/bash.php?script=getnzbs');
    die();
}
require 'secure/settings.php';
if ($home) {
    echo '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Floorplan2</title>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
<meta name="HandheldFriendly" content="true"/>
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="viewport" content="width=device-width,height=device-height,user-scalable=yes,minimal-ui"/>
<link rel="icon" type="image/png" href="images/domoticzphp48.png"/>
<link rel="shortcut icon" href="images/domoticzphp48.png"/>
<link rel="apple-touch-icon" href="images/domoticzphp48.png"/>
<meta name="msapplication-TileImage" content="images/domoticzphp48.png">
<meta name="mobile-web-app-capable" content="yes">
<link rel="stylesheet" type="text/css" href="/styles/floorplan2.php">
<style>
	.btn{height:85px;}
</style>
</head><body>';
    if (isset($_POST['Schakel'])) {
        sw($_POST['Naam'], $_POST['Actie']);
    } elseif (isset($_REQUEST['filmfriends'])) {
        shell_exec('curl -s "http://192.168.2.10/filmfriends.php" > /dev/null 2>/dev/null &');
    } elseif (isset($_REQUEST['getnzbs'])) {
        @file_get_contents('http://192.168.2.10/bash.php?script=getnzbs');
    } elseif (isset($_POST['RestartDomoticz'])) {
        shell_exec('sudo service domoticz restart &');
    } elseif (isset($_POST['RestartPihole'])) {
        exec('nohup sudo /sbin/reboot &');
    }

    echo '<form method="POST"><div class="fix center clock"><a href=\'javascript:navigator_Go("floorplan3.php");\'><h2>'.strftime("%k:%M:%S", TIME).'</h2></a></div>
<div class="fix z1" style="top:5px;left:5px;"><a href=\'javascript:navigator_Go("floorplan.php");\'><img src="/images/close.png" width="72px" height="72px"/></a></div>
<div class="fix box box1" style="top:120px;left:0px;width:500px">
	<input type="submit" name="RestartDomoticz" value="Restart Domoticz" class="btn b2"/>
	<input type="submit" name="RestartPihole" value="Restart PiHole" class="btn b2"/>
</div>
<div class="fix box box2" style="top:230px;left:0px;width:500px">
	<input type="submit" name="Healnetwork" value="Heal Network" class="btn b2"/>
	<input type="submit" name="Healnetwork" value="Refill APCu cache" class="btn b2"/>
	<input type="submit" name="getnzbs" value="Get NZBs" class="btn b2"/>
	<input type="submit" name="filmfriends" value="Film Friends" class="btn b2"/>
</div>
</div>
<div class="fix box box4" style="top:680px;left:0px;height:165px;width:500px">&nbsp;'.$udevice.'<br/>&nbsp'.$_SERVER['HTTP_USER_AGENT'].'
</form>
<div class="fix z1 logout"><form method="POST"><input type="hidden" name="username" value="'.$user.'"/><input type="submit" name="logout" value="Logout" class="btn" style="padding:0px;margin:0px;width:90px;height:35px;"/></form><br/><br/></div></div>';
}?>
<script type="text/javascript">
function navigator_Go(url) {window.location.assign(url);}
</script>
</body></html>

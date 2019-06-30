<?php
/**
 * Pass2PHP
 * php version 7.3.4-2
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
require '../secure/functions.php';
$_SESSION['referer']='picam1/index.php';
require '../secure/authentication.php';
if ($home) {
    echo '<html><head><title>Voordeur - Oprit</title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
    <meta name="HandheldFriendly" content="true"/>
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="viewport" content="width=device-width,height=device-height,user-scalable=yes,initial-scale=0.5,minimal-ui" />
    <link rel="icon" type="image/png" href="/images/Camera.png"/>
    <link rel="shortcut icon" href="/images/Camera.png"/>
    <link rel="apple-touch-icon" href="/images/Camera.png"/>
    <meta name="mobile-web-app-capable" content="yes"/>
    <link href="/styles/picam1.php" rel="stylesheet" type="text/css"/>
    </head><body>';
    if(isset($_POST['Record'])){
        file_get_contents("http://192.168.2.11/fifo_command.php?cmd=record%20on%205%2055");
        file_get_contents("http://192.168.2.13/fifo_command.php?cmd=record%20on%205%2055");
    }elseif(isset($_POST['Foto'])){
        shell_exec('curl -s "http://192.168.2.11/telegram.php?snapshot=true" &');
        shell_exec('curl -s "http://192.168.2.13/telegram.php?snapshot=true" &');
    }elseif(isset($_POST['Licht'])){
        sw('voordeur');
    }
    echo '<div class="navbar" role="navigation">
        <form method="POST" action="../floorplan.php">
          <input type="submit" value="Plan" class="btn b7" />
        </form>
        <form method="POST">
          <input type="submit" value="Record" name="Record" class="btn b7"/>
          <input type="submit" value="Foto" name="Foto" class="btn b8"/>
          <input type="submit" value="Licht" name="Licht" class="btn b7"/>
          <input type="submit" value="Refresh" name="Refresh" class="btn b7"/>
        </form>
        <form method="POST" action="media-archive.php">
            <input type="hidden" name="type" value="videos"/>
            <input type="hidden" name="year" value="'.date("Y").'"/>
            <input type="hidden" name="m0" value="'.date("n",time()-86400).'"/>
            <input type="hidden" name="d0" value="'.date("j",time()-86400).'"/>
            <input type="hidden" name="m1" value="'.date("n").'"/>
            <input type="hidden" name="d1" value="'.date("j").'"/>
            <input type="submit" value="Archief" name="Archief" class="btn b7"/>
        </form>
        </div>
        <div class="fix camera1">
            <img class="camerai" id="mjpeg_dest" src="jpg.php"/>
        </div>
        <div class="fix camera2">
            <img class="camerai" id="mjpeg_destoprit" src="jpg.oprit.php"/>
        </div>
        <script type="text/javascript">
        function navigator_Go(url) {window.location.assign(url);}
        </script>
        <script type="text/javascript">
        for (var i = 1; i < 99999; i++){
     		try{window.clearInterval(i);}catch{};
     	}
		mypicam=setInterval(getpic, 200);
        function getpic(){
        	document.getElementById(\'mjpeg_destoprit\').src = "jpg.oprit.php?random="+new Date().getTime();
        	document.getElementById(\'mjpeg_dest\').src = "jpg.php?random="+new Date().getTime();
        }
        </script>
        </body></html>
    ';
}
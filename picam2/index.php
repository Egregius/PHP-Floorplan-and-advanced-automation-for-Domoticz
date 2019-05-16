<?php //Alex
require('../secure/functions.php');
require('../secure/authentication.php');
if($home){
    require(dirname(__FILE__) . '/config.php');
    echo '<html><head><title>'.TITLE_STRING.'</title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
    <meta name="HandheldFriendly" content="true"/>
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="viewport" content="width=device-width,height=device-height,user-scalable=yes,initial-scale=0.5,minimal-ui" />
    <link rel="icon" type="image/png" href="/images/Camera.png"/>
    <link rel="shortcut icon" href="/images/Camera.png"/>
    <link rel="apple-touch-icon" href="/images/Camera.png"/>
    <meta name="mobile-web-app-capable" content="yes"/>
    <link href="/styles/picam2.php" rel="stylesheet" type="text/css"/>
    </head><body>';
    if (isset($_POST['Record'])) {
        file_get_contents("http://192.168.2.12/fifo_command.php?cmd=record%20on%205%2055");
    } elseif (isset($_POST['Foto'])) {
        shell_exec('curl -s "http://192.168.2.12/telegram.php?snapshot=true" &');
    }
    echo '<div class="navbar" role="navigation">
            <form method="POST" action="../floorplan.php">
          <input type="submit" value="Plan" class="btn b5"/>
        </form>
        <form method="POST">
          <input type="submit" value="Record" name="Record" class="btn b5"/>
          <input type="submit" value="Refresh" name="Refresh" class="btn b5"/>
        </form>
        </div>
        <div class="fix camera">
            <a href=""><img class="camerai" id="mjpeg_destalex" src="jpg.php"/></a>
        </div>
        <script type="text/javascript">
        function navigator_Go(url) {window.location.assign(url);}
        window.setInterval(function()
        {
            document.getElementById(\'mjpeg_destalex\').src = "jpg.php?random="+new Date().getTime();
        }, '. 1000/2.');
    </script></body></html>
    ';
}
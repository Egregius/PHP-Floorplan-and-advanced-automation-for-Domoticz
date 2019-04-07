<?php //Alex
require('../secure/settings.php');
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
    if (isset($_REQUEST['refreshrate'])) {
        $_SESSION['refreshrate']=$_REQUEST['refreshrate'];
    }
    if (!isset($_SESSION['refreshrate'])) {
        $_SESSION['refreshrate']=8;
        if($local===false&&$udevice=='iPhone')$_SESSION['refreshrate']=2;
    }
    if (isset($_POST['Record'])) {
        file_get_contents("http://192.168.2.212/fifo_command.php?cmd=record%20on%205%2055");
    } elseif (isset($_POST['Foto'])) {
        shell_exec('curl -s "http://192.168.2.212/telegram.php?snapshot=true" &');
    }
    echo '<div class="navbar" role="navigation">
            <form method="POST" action="../floorplan.php">
          <input type="submit" value="Plan" class="btn b5"/>
        </form>
        <form method="POST">
          <input type="submit" value="Record" name="Record" class="btn b5"/>
          <input type="submit" value="Refresh" name="Refresh" class="btn b5"/>
          <select name="refreshrate" class="btn b10" onchange="this.form.submit()" >';
          $items=array(0.5,1,2,3,4,5,6,7,8);
          foreach ($items as $i) {
            echo '
            <option value="'.$i.'" '.($_SESSION['refreshrate']==$i?'selected':'').'>'.$i.' fr/ sec</option>';
        }
        echo '
        </select>
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
        }, '. 1000/$_SESSION['refreshrate'].');
    </script></body></html>
    ';
}
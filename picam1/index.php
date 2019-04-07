<?php //voordeur-oprit
require('../secure/settings.php');
if($home){
//require(dirname(__FILE__) . '/config.php');
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
if(isset($_REQUEST['refreshrate']))$_SESSION['refreshrate']=$_REQUEST['refreshrate'];
if(!isset($_SESSION['refreshrate'])){
	if ($local===false&&$udevice=='iPhone') {
	    $_SESSION['refreshrate']=1;
	    $refresh=1000/$_SESSION['refreshrate'];
        $refresh2=$refresh;
	} else {
	    $_SESSION['refreshrate']=8;
	    $refresh=1000/$_SESSION['refreshrate'];
        $refresh2=round(2000/$_SESSION['refreshrate']);
	}
}

if(isset($_POST['Record'])){
	file_get_contents("http://192.168.2.211/fifo_command.php?cmd=record%20on%205%2055");
	file_get_contents("http://192.168.2.213/fifo_command.php?cmd=record%20on%205%2055");
}elseif(isset($_POST['Foto'])){
	shell_exec('curl -s "http://192.168.2.211/telegram.php?snapshot=true" &');
	shell_exec('curl -s "http://192.168.2.213/telegram.php?snapshot=true" &');
}elseif(isset($_POST['Licht'])){
	sw('voordeur');
}
echo '<div class="navbar" role="navigation">
	<form method="POST" action="../floorplan.php">
      <input type="submit" value="Plan" class="btn b6" />
    </form>
    <form method="POST">
      <input type="submit" value="Record" name="Record" class="btn b7"/>
      <input type="submit" value="Foto" name="Foto" class="btn b7"/>
      <input type="submit" value="Licht" name="Licht" class="btn b7"/>
      <select name="refreshrate" class="btn b7" onchange="this.form.submit()" >';
      $items=array(0.5,1,2,3,4,5,6,7,8);
      foreach ($items as $i) {
        echo '
        <option value="'.$i.'" '.($_SESSION['refreshrate']==$i?'selected':'').'>'.$i.' fr/ sec</option>';
	}
	echo '
	</select>
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
		<a href=""><img class="camerai" id="mjpeg_destvoordeur" src="jpg.php"/></a>
	</div>
	<div class="fix camera2">
		<a href=""><img class="camerai" id="mjpeg_destoprit" src="jpg.oprit.php"/></a>
	</div>
	<script type="text/javascript">
	function navigator_Go(url) {window.location.assign(url);}
	</script>
	<script type="text/javascript">
	window.setInterval(function()
	{
		document.getElementById(\'mjpeg_destoprit\').src = "jpg.oprit.php?random="+new Date().getTime();
	}, '.$refresh.');
</script><script type="text/javascript">
	window.setInterval(function()
	{
		document.getElementById(\'mjpeg_destvoordeur\').src = "jpg.php?random="+new Date().getTime();
	}, '.$refresh2 .');
	</script>
	</body></html>
';
}
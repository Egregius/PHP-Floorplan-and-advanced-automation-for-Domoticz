<?php
/**
 * Pass2PHP
 * php version 7.0.33
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
$start=microtime(true);
require 'secure/settings.php';
if ($home) {
    error_reporting(E_ALL);ini_set("display_errors", "on");
    echo '<html>
	<head>
		<title>Floorplan</title>
		<meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
		<meta name="HandheldFriendly" content="true"/>
		<meta name="mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<meta name="viewport" content="width=device-width,height=device-height,user-scalable=yes,minimal-ui"/>
		<link rel="icon" type="image/png" href="images/domoticzphp48.png"/>
		<link rel="shortcut icon" href="images/domoticzphp48.png"/>
		<link rel="apple-touch-icon" href="images/domoticzphp48.png"/>
		<link rel="stylesheet" type="text/css" href="/styles/floorplan.php">
	</head>
	<body>
		<div class="fix" style="top:5px;left:5px;">
			<a href=\'javascript:navigator_Go("floorplan.php");\'>
				<img src="/images/close.png" width="72px" height="72px"/>
			</a>
		</div>
		<br>
		<br>
		<br>';
    $devices=@json_decode(@file_get_contents('http://127.0.0.1:8080/json.htm?type=command&param=getplandevices&idx=4', true, $ctx), true);
    echo '
		<form method="GET">
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<select name="idx" class="btn" onchange="this.form.submit()" >';

    foreach ($devices['result'] as $d) {
        if (isset($_REQUEST['idx'])&&$_REQUEST['idx']==$d['devidx']) {
            echo '
				<option value="'.$d['devidx'].'" selected>'.$d['Name'].'</option>';
        } else {
            echo '
			<option value="'.$d['devidx'].'">'.$d['Name'].'</option>';
        }
    }
    echo '
			</select>
		</form>';
    if (isset($_REQUEST['idx'])) {
        $idx=$_REQUEST['idx'];
    } else {
        $idx=$devices['result'][0]['devidx'];
    }
    echo '
		<table>';
    $ctx=stream_context_create(array('http'=>array('timeout' => 2)));
    $datas=@json_decode(@file_get_contents('http://127.0.0.1:8080/json.htm?type=lightlog&idx='.$idx, true, $ctx), true);
    //print_r($datas);
    $status='';$tijdprev=TIME;$totalon=0;
    if (!empty($datas['result'])) {
        foreach ($datas['result'] as $data) {
            $status=$data['Data'];
            $tijd=strtotime($data['Date']);
            if ($tijd<$eendag) {
                break;
            }
            $period=($tijdprev-$tijd);
            if ($status=='Off') {
                $style="color:#1199FF";
            } else {
                $totalon=$totalon+$period;
                $style="color:#FF4400";
            }
            $tijdprev=$tijd;
            echo '
			<tr>
				<td style="'.$style.'">'.$data['Date'].'</td>
				<td style="'.$style.'">&nbsp;'.$status.'&nbsp;</td>
				<td style="'.$style.'">&nbsp;'.convertToHours($period).'</td>
			</tr>';
        }
    }
    echo '
		</table>
		<div class="fix" style="top:0px;left:204px;width:60px;font-size:2em"><a href="?idx='.$idx.'">'.convertToHours($totalon).'</a></div>
		<script type="text/javascript">
			function navigator_Go(url) {window.location.assign(url);}
		</script>';
}
?>

    </body>
</html>
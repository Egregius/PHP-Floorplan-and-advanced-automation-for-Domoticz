<?php
$d=fetchdata();
$user='cron360';
if(isset($db)) $db=dbconnect();


if ($d['bose103']['s']=='On'&&($d['Weg']['s']==1||$time<=strtotime('3:00'))) {
	$nowplaying=@json_decode(@json_encode(@simplexml_load_string(@file_get_contents('http://192.168.2.103:8090/now_playing'))),true);
	if (!empty($nowplaying)) {
		if (isset($nowplaying['@attributes']['source'])) {
			if ($nowplaying['@attributes']['source']!='STANDBY') {
				$volume=json_decode(json_encode(simplexml_load_string(file_get_contents("http://192.168.2.103:8090/volume")	)),true);
				$cv=$volume['actualvolume']-1;
				if ($cv<=5) {
					bosekey("POWER", 0, 103);
					sw('bose103', 'Off', basename(__FILE__).':'.__LINE__);
				} else bosevolume($cv, 103, basename(__FILE__).':'.__LINE__);
			} else sw('bose103', 'Off', basename(__FILE__).':'.__LINE__);
		}
	}
}

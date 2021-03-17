<?php
/**
 * Pass2PHP
 * php version 7.3
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link	 https://egregius.be
 **/
$user='cron3600';
if (!isset($db)) $db=dbconnect();
$date=strftime("%F", TIME);
if (strftime("%k", TIME)==19) {
	$xml=json_decode(json_encode(	simplexml_load_string(file_get_contents('/temp/domoticz/Config/ozwcache_0xe9238f6e.xml'),"SimpleXMLElement",	LIBXML_NOCDATA)),true);
	$msg='';
	foreach ($xml['Node'] as $node) {
		foreach ($node['CommandClasses']['CommandClass'] as $cmd) {
			if (isset($cmd['Value']['@attributes']['label'])) {
				if ($cmd['Value']['@attributes']['label']=='Battery Level') {
					$id=$node['@attributes']['id'];
					$name=$node['@attributes']['name'];
					$value=$cmd['Value']['@attributes']['value'];
					if ($value>100) 	$value=100;
					$stmt=$db->query("select value from battery WHERE name='$name' ORDER BY `date` DESC LIMIT 0,1;");
					while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) $prev=$row['value'];
					if (isset( $prev)&&$value!=$prev) $msg.=$name.PHP_EOL.'  new = '.$value.', prev = '.$prev.PHP_EOL.PHP_EOL;
					unset( $prev);
					$query="INSERT INTO `battery` (`date`,`name`,`value`) VALUES ('$date','$name','$value') ON DUPLICATE KEY UPDATE `value`='$value';";
					if (!$result=$db->query($query)) die('There was an error running the query ['.$query.'-'.$db->error.']');
				}
			}
		}
	}
	unset($xml);
	if (strlen($msg)>5) telegram($msg);
}
$data=json_decode(file_get_contents('http://192.168.2.2:8080/json.htm?type=devices&rid=1'), true);
if (isset($data['CivTwilightStart'])) {
	$time=TIME;
	$name='civil_twilight';
	$status=strtotime($data['CivTwilightStart']);
	$mode=strtotime($data['CivTwilightEnd']);
	$db->query("INSERT INTO devices (n,s,m,t) VALUES ('$name','$status','$mode','$time') ON DUPLICATE KEY UPDATE s='$status', m='$mode', t='$time';");
	$name='Sun';
	$status=strtotime($data['Sunrise']);
	$mode=strtotime($data['Sunset']);
	$icon=strtotime($data['SunAtSouth']);
	$db->query("INSERT INTO devices (n,s,m,icon,t) VALUES ('$name', '$status', '$mode', '$icon', '$time') ON DUPLICATE KEY UPDATE s='$status', m='$mode', icon='$icon', t='$time';");
	if (TIME>$status&&TIME<$mode) {
		$uv=json_decode(shell_exec("curl -X GET 'https://api.openuv.io/api/v1/uv?lat=".$lat."&lng=".$lon."' -H 'x-access-token: ".$openuv."'"),true);
		if (isset($uv['result'])) {
			if ($uv['result']['uv']!=$d['uv']['s']) store('uv', round($uv['result']['uv'], 1), basename(__FILE__).':'.__LINE__);
			if ($uv['result']['uv_max']!=$d['uv']['m']) storemode('uv', round($uv['result']['uv_max'], 1), basename(__FILE__).':'.__LINE__);
		}
	}
}

//Update and clean SQL database

//Putting buiten temp to verbruik.egregius.be
$stmt = $db->query(
	"SELECT
		left(stamp,10) as stamp,
		min(buiten_min) as buiten_min,
		max(buiten_max) as buiten_max,
		avg(buiten_avg) as buiten_avg
	FROM temp_hour
	GROUP BY left(stamp,10)
	ORDER BY `stamp` DESC
	LIMIT 0,10"
);
$dbe=new mysqli('192.168.2.20', 'home', 'H0m€', 'verbruik');
if ($dbe->connect_errno>0) die('Unable to connect to database ['.$dbe->connect_error.']');
while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
	$stamp=$row['stamp'];
	$buiten_min=$row['buiten_min'];
	$buiten_max=$row['buiten_max'];
	$buiten_avg=$row['buiten_avg'];
	$query = "
		INSERT INTO `temp_buiten`
			(`stamp`,`min`,`max`,`avg`)
		VALUES
			('$stamp','$buiten_min','$buiten_max','$buiten_avg')
		ON DUPLICATE KEY UPDATE
			`min`='$buiten_min',`max`='$buiten_max',`avg`='$buiten_avg';";
	if (!$result = $dbe->query($query)) {
		die('There was an error running the query ['.$query.'-'.$dbe->error.']');
	}
}

/*
if ($d['buiten_temp']['s']>2&&$d['buiten_temp']['s']<30) {
	$low=40;
	$high=40;
} elseif ($d['buiten_temp']['s']< -5||$d['buiten_temp']['s']>35) {
	$low=60;
	$high=100;
} else {
	$low=50;
	$high=70;
}
$daikin=json_decode($d['daikinliving']['s']);
if ($daikin->adv == '') {
	$powermode=0;
} else if (strstr($daikin->adv, '/')) {
	$advs=explode("/", $daikin->adv);
	if ($advs[0]==2) $powermode=2;
	else if ($advs[0]==12) $powermode=1;
	else $powermode=0;
} else {
	if ($daikin->adv==13)  $powermode=0; //Normal
	else if ($daikin->adv==12)  $powermode=1; // Eco
	else if ($daikin->adv==2)  $powermode=2; // Power
	else if ($daikin->adv=='')  $powermode=0;
}
if ($powermode<2) {
	if (TIME>=strtotime('5:00')&&TIME<strtotime('20:00')) {
		file_get_contents('http://192.168.2.111/aircon/set_demand_control?type=1&en_demand=1&mode=2&max_pow='.$high.'&scdl_per_day=4&moc=0&tuc=0&wec=0&thc=0&frc=0&sac=0&suc=0');
	} else {
		file_get_contents('http://192.168.2.111/aircon/set_demand_control?type=1&en_demand=1&mode=2&max_pow='.$low.'&scdl_per_day=4&moc=0&tuc=0&wec=0&thc=0&frc=0&sac=0&suc=0');
	}
}
$daikin=json_decode($d['daikinkamer']['s']);
if ($daikin->adv == '') {
	$powermode=0;
} else if (strstr($daikin->adv, '/')) {
	$advs=explode("/", $daikin->adv);
	if ($advs[0]==2) $powermode=2;
	else if ($advs[0]==12) $powermode=1;
	else $powermode=0;
} else {
	if ($daikin->adv==13)  $powermode=0; //Normal
	else if ($daikin->adv==12)  $powermode=1; // Eco
	else if ($daikin->adv==2)  $powermode=2; // Power
	else if ($daikin->adv=='')  $powermode=0;
}
if ($powermode<2) {
	if (TIME>=strtotime('5:00')&&TIME<strtotime('21:00')) {
		file_get_contents('http://192.168.2.112/aircon/set_demand_control?type=1&en_demand=1&mode=2&max_pow='.$low.'&scdl_per_day=4&moc=0&tuc=0&wec=0&thc=0&frc=0&sac=0&suc=0');
	} else {
		file_get_contents('http://192.168.2.112/aircon/set_demand_control?type=1&en_demand=1&mode=2&max_pow='.$high.'&scdl_per_day=4&moc=0&tuc=0&wec=0&thc=0&frc=0&sac=0&suc=0');
	}
}
$daikin=json_decode($d['daikinalex']['s']);
if ($daikin->adv == '') {
	$powermode=0;
} else if (strstr($daikin->adv, '/')) {
	$advs=explode("/", $daikin->adv);
	if ($advs[0]==2) $powermode=2;
	else if ($advs[0]==12) $powermode=1;
	else $powermode=0;
} else {
	if ($daikin->adv==13)  $powermode=0; //Normal
	else if ($daikin->adv==12)  $powermode=1; // Eco
	else if ($daikin->adv==2)  $powermode=2; // Power
	else if ($daikin->adv=='')  $powermode=0;
}
if ($powermode<2) {
	if (TIME>=strtotime('5:00')&&TIME<strtotime('19:00')) {
		file_get_contents('http://192.168.2.113/aircon/set_demand_control?type=1&en_demand=1&mode=2&max_pow='.$low.'&scdl_per_day=4&moc=0&tuc=0&wec=0&thc=0&frc=0&sac=0&suc=0');
	} else {
		file_get_contents('http://192.168.2.113/aircon/set_demand_control?type=1&en_demand=1&mode=2&max_pow='.$high.'&scdl_per_day=4&moc=0&tuc=0&wec=0&thc=0&frc=0&sac=0&suc=0');
	}
}
*/
/*foreach (array('living', 'kamer', 'alex') as $k) {
	file_get_contents('http://192.168.2.'.$ip.'/aircon/set_special_mode?en_streamer=0');
	sleep(2);
}*/

if (TIME<strtotime('1:05')) {
	for ($x=3;$x>=0;$x--) {
		$date=date("Y-m-d", (TIME-($x*86400)));
		$query="INSERT IGNORE INTO `pluvio` (`date`, `rain`) VALUES ('$date', '0');";
		if(!$result=$db->query($query)){lg($db->error);die('There was an error running the query ['.$query.'-'.$db->error.']');}
	}
}
/* Clean old database records */
$remove=strftime("%F %T", TIME-90000);
$stmt=$db->query("delete from temp where stamp < '$remove'");
$remove=strftime("%F %T", TIME-200000);
$stmt=$db->query("delete from regen where stamp < '$remove'");

//RefreshZwave(128);
if ($d['buiten_temp']['s']<3&&$d['heating']['s']<4&&past('heating')>43200) store('heating', 4, basename(__FILE__).':'.__LINE__);
elseif ($d['buiten_temp']['s']>15&&$d['heating']['s']>1&&past('heating')>43200) store('heating', 1, basename(__FILE__).':'.__LINE__);

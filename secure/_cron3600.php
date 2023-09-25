<?php
//lg('CRON-3600');
if (!isset($db)) $db=dbconnect();
$d=fetchdata();
$dag=dag();
$time=time();
$user='cron3600';
$date=strftime("%F", $time);
if (strftime("%k", $time)==19) {
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
if (strftime("%k", $time)==0&&$d['winst']['s']!=0) store ('winst', 0);

$data=json_decode(file_get_contents('http://127.0.0.1:8080/json.htm?type=command&param=getdevices&rid=1'), true);
if (isset($data['CivTwilightStart'])) {
	$time=$time;
	$name='civil_twilight';
	$status=strtotime($data['CivTwilightStart']);
	$mode=strtotime($data['CivTwilightEnd']);
	$db->query("INSERT INTO devices (n,s,m,t) VALUES ('$name','$status','$mode','$time') ON DUPLICATE KEY UPDATE s='$status', m='$mode', t='$time';");
	$name='Sun';
	$status=strtotime($data['Sunrise']);
	$mode=strtotime($data['Sunset']);
	$icon=strtotime($data['SunAtSouth']);
	$db->query("INSERT INTO devices (n,s,m,icon,t) VALUES ('$name', '$status', '$mode', '$icon', '$time') ON DUPLICATE KEY UPDATE s='$status', m='$mode', icon='$icon', t='$time';");
} else lg('Error fetching CivTwilightStart from domoticz');

/* Clean old database records */

$remove=strftime("%F %T", $time-(86400*100));
$stmt=$db->query("delete from temp where stamp < '$remove'");

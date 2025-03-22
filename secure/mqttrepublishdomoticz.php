#!/usr/bin/php
<?php
require '/var/www/vendor/autoload.php';
require '/var/www/html/secure/functions.php';
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

if (!isset($db)) $db=dbconnect(basename(__FILE__).':'.__LINE__);
$stmt=$db->query("SELECT n,s,t,m,dt,icon,ajax FROM devices WHERE ajax>=1 OR n like '%_hum';");
while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
	$d[$row['n']]['s']=$row['s'];
	if($row['ajax']==2)$d[$row['n']]['t']=$row['t'];
	if(!is_null($row['m']))$d[$row['n']]['m']=$row['m'];
	if(!is_null($row['dt']))$d[$row['n']]['dt']=$row['dt'];
	if(!is_null($row['icon']))$d[$row['n']]['icon']=$row['icon'];
}
try {
    $mqtt=new MqttClient('127.0.0.1',1883,'mqttrepublishdomoticz'.rand());
    $connectionSettings=(new ConnectionSettings())
        ->setKeepAliveInterval(60)
        ->setUseTls(false);
    $mqtt->connect($connectionSettings, true);
    echo "✅ ".date("H:i:s")."  Verbonden met MQTT\n";
    $mqtt->subscribe('domoticz/out/#', function ($t, $m) use ($mqtt, $d) {
        $t=str_replace('domoticz/out/','',$t);
        $m=json_decode($m,true);
        $name=$m['name'];
		$topic='i/'.$name;
		if (array_key_exists($name, $d)&&$m['stype']!='Viking 02035, 02038, TSS320') {
			if ($m['dtype']=='Light/Switch') {
				if ($m['switchType']=='Dimmer') {
					if ($m['nvalue']==0) $d[$name]['s']=0;
					else $d[$name]['s']=$m['svalue1'];
				} elseif ($m['switchType']=='Blinds Percentage') {
					if ($m['nvalue']==0) $d[$name]['s']=0;
					elseif ($m['nvalue']==1) $d[$name]['s']=100;
					else $d[$name]['s']=$m['svalue1'];
				} elseif ($m['switchType']=='Contact') {
					if ($m['nvalue']==0) $d[$name]['s']='Closed';
					elseif ($m['nvalue']==1) $d[$name]['s']='Open';
				} elseif ($m['switchType']=='Door Contact') {
					if ($name=='achterdeur') {
						if ($m['nvalue']==0) $d[$name]['s']='Open';
						elseif ($m['nvalue']==1) $d[$name]['s']='Closed';
					} else {
						if ($m['nvalue']==0) $d[$name]['s']='Closed';
						elseif ($m['nvalue']==1) $d[$name]['s']='Open';
					}
				} elseif ($m['switchType']=='On/Off') {
					if ($m['nvalue']==0) $d[$name]['s']='Off';
					elseif ($m['nvalue']==1) $d[$name]['s']='On';
				} elseif ($m['switchType']=='Motion Sensor') {
					if ($m['nvalue']==0) $d[$name]['s']='Off';
					elseif ($m['nvalue']==1) $d[$name]['s']='On';
				} else {
					if ($m['nvalue']==0) $d[$name]['s']='Off';
					elseif ($m['nvalue']==1) $d[$name]['s']='On';
				}
			} elseif ($m['dtype']=='Lighting 2') {
				if ($m['nvalue']==0) $d[$name]['s']='Off';
				elseif ($m['nvalue']==1) $d[$name]['s']='On';
			} elseif ($m['dtype']=='Temp') {
				$d[$name]['s']=$m['svalue1'];
			} elseif ($m['dtype']=='General') {
				if ($m['stype']=='kWh') {
					$d[$name]['s']=$m['svalue1'];
				}
			} elseif ($m['dtype']=='Usage') {
				$d[$name]['s']=$m['svalue1'];
			} elseif ($m['dtype']=='Color Switch') {
				$d[$name]['s']=$m['nvalue'];
			} elseif ($m['dtype']=='Setpoint') {
				$status=(float)$m['svalue1'];
			}
			if (isset($d[$name]['t'])) $d[$name]['t']=time();
			$status=json_encode($d[$name]);
			echo "🚀 ".date("H:i:s")."  $topic $status\n";
			$mqtt->publish($topic, $status, 0, true);
			unset($name,$dtype,$topic,$status);
		} elseif ($m['stype']=='Viking 02035, 02038, TSS320') {
			if ($m['name']=='buiten_hum') { // 1
				$temp=$m['svalue1'];
				$hum=$m['svalue2']+1;
				if ($hum>100) $hum=100;
				elseif($hum>$d['buiten_temp']['m']+1) $hum=$d['buiten_temp']['m']+1;
				elseif($hum<$d['buiten_temp']['m']-1) $hum=$d['buiten_temp']['m']-1;
				if($hum!=$d['buiten_temp']['m']) {
					$d['buiten_temp']['m']=$hum;
					$topic='i/buiten_temp';
					$status=json_encode($d['buiten_temp']);
					$mqtt->publish($topic, $status, 0, true);
					echo "🚀 ".date("H:i:s")."  $topic $status\n";
				}
				if ($temp!=$d['minmaxtemp']['icon']) {
					$d['minmaxtemp']['icon']=$temp;
					$topic='i/minmaxtemp';
					$status=json_encode($d['minmaxtemp']);
					$mqtt->publish($topic, $status, 0, true);
					echo "🚀 ".date("H:i:s")."  $topic $status\n";
				}
			} elseif ($m['name']=='kamer_hum') { // 2
				$hum=$m['svalue2']-7;
				if ($hum>100) $hum=100;
				elseif($hum>$d['kamer_temp']['m']+1) $hum=$d['kamer_temp']['m']+1;
				elseif($hum<$d['kamer_temp']['m']-1) $hum=$d['kamer_temp']['m']-1;
				if ($hum!=$d['kamer_temp']['m']) {
					$d['kamer_temp']['m']=$hum;
					$topic='i/kamer_temp';
					$status=json_encode($d['kamer_temp']);
					$mqtt->publish($topic, $status, 0, true);
					echo "🚀 ".date("H:i:s")."  $topic $status\n";
				}
			} elseif ($m['name']=='alex_hum') { // 3
				$hum=$m['svalue2']-9;
				if ($hum>100) $hum=100;
				elseif($hum>$d['alex_temp']['m']+1) $hum=$d['alex_temp']['m']+1;
				elseif($hum<$d['alex_temp']['m']-1) $hum=$d['alex_temp']['m']-1;
				if ($hum!=$d['alex_temp']['m']) {
					$d['alex_temp']['m']=$hum;
					$topic='i/alex_temp';
					$status=json_encode($d['alex_temp']);
					$mqtt->publish($topic, $status, 0, true);
					echo "🚀 ".date("H:i:s")."  $topic $status\n";
				}
			} elseif ($m['name']=='waskamer_hum') { // 4
				$status=$m['svalue1'];
				$hum=$m['svalue2']+3;
				if ($hum>100) $hum=100;
				elseif($hum>$d['waskamer_temp']['m']+1) $hum=$d['waskamer_temp']['m']+1;
				elseif($hum<$d['waskamer_temp']['m']-1) $hum=$d['waskamer_temp']['m']-1;
				if ($status!=$d['waskamer_temp']['s']||$hum!=$d['waskamer_temp']['m']) {
					$d['waskamer_temp']['s']=$status;
					$topic='i/waskamer_temp';
					$status=json_encode($d['waskamer_temp']);
					$mqtt->publish($topic, $status, 0, true);
					echo "🚀 ".date("H:i:s")."  $topic $status\n";
				}
			} elseif ($m['name']=='badkamer_hum') { // 5
				$hum=$m['svalue2']-7;
				if ($hum>100) $hum=100;
				elseif($hum>$d['badkamer_temp']['m']+1) $hum=$d['badkamer_temp']['m']+1;
				elseif($hum<$d['badkamer_temp']['m']-1) $hum=$d['badkamer_temp']['m']-1;
				if ($hum!=$d['badkamer_temp']['m']) {
					$d['badkamer_temp']['m']=$hum;
					$topic='i/badkamer_temp';
					$status=json_encode($d['badkamer_temp']);
					$mqtt->publish($topic, $status, 0, true);
					echo "🚀 ".date("H:i:s")."  $topic $status\n";
				}
			} elseif ($m['name']=='living_hum') { // 6
				$hum=$m['svalue2']-3;
				if ($hum>100) $hum=100;
				elseif($hum>$d['living_temp']['m']+1) $hum=$d['living_temp']['m']+1;
				elseif($hum<$d['living_temp']['m']-1) $hum=$d['living_temp']['m']-1;
				if ($hum!=$d['living_temp']['m']) {
					$d['living_temp']['m']=$hum;
					$topic='i/living_temp';
					$status=json_encode($d['living_temp']);
					$mqtt->publish($topic, $status, 0, true);
					echo "🚀 ".date("H:i:s")."  $topic $status\n";
				}
			} else echo "Name not found\n";
		} else {
//			echo "Nothing to do\n";
//			echo "$t	".print_r($m,true)."\n";
		}
    }, 0);
    while ($mqtt->loop(true)) {}
    $mqtt->disconnect();
} catch (Exception $e) {
    echo "❌ Fout: " . $e->getMessage() . "\n";
}

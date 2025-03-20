<?php
require '/var/www/vendor/autoload.php';
require '/var/www/html/secure/functions.php';
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
$subscribeTopic="domoticz/out/#";

if (!isset($db)) $db=dbconnect(basename(__FILE__).':'.__LINE__);
$stmt=$db->query("SELECT n,s,t,m,dt,icon,ajax FROM devices WHERE ajax>=1;");
while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
	$d[$row['n']]['s']=$row['s'];
	if($row['ajax']==2)$d[$row['n']]['t']=$row['t'];
	if(!empty($row['m']))$d[$row['n']]['m']=$row['m'];
	if(!empty($row['dt']))$d[$row['n']]['dt']=$row['dt'];
	if(!empty($row['icon']))$d[$row['n']]['icon']=$row['icon'];
}
try {
    $mqtt=new MqttClient('127.0.0.1',1883,'php_mqtt_ws_'.rand());
    $connectionSettings=(new ConnectionSettings())
        ->setKeepAliveInterval(60)
        ->setUseTls(false);
    $mqtt->connect($connectionSettings, true);
    echo "✅ Verbonden met MQTT\n";
    $mqtt->subscribe('domoticz/out/#', function ($t, $m) use ($mqtt, $d) {
		echo "📩 Ontvangen: [$t] $m\n";
        $t=str_replace('domoticz/out/','',$t);
        $m=json_decode($m,true);
        $name=$m['name'];
        if (array_key_exists($name, $d)) {
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
			}
			if (isset($d[$name]['t'])) $d[$name]['t']=time();
			$status=json_encode($d[$name]);
			$topic='i/'.$name;
			echo "🚀 Herpublicatie: $topic $status\n";
			$mqtt->publish($topic, $status, 0, true);
			unset($name,$dtype,$topic,$status);
		} elseif ($m['dtype']=='Setpoint') {
			$status=(float)$m['svalue1'];
		}
    }, 0);
    while ($mqtt->loop(true)) {}
    $mqtt->disconnect();
} catch (Exception $e) {
    echo "❌ Fout: " . $e->getMessage() . "\n";
}

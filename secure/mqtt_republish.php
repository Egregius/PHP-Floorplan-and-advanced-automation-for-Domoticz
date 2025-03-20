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
        $m=json_decode($m);
        $name=$m->name;
        if (array_key_exists($name, $d)) {
			$status=$d[$name];
			$status=json_encode($status);
			$topic='i/'.$name;
			echo "🚀 Herpublicatie: $topic $status\n";
			$mqtt->publish($topic, $status, 0, true);
			unset($name,$dtype,$topic,$status);
		}
    }, 0);
    while ($mqtt->loop(true)) {}
    $mqtt->disconnect();
} catch (Exception $e) {
    echo "❌ Fout: " . $e->getMessage() . "\n";
}

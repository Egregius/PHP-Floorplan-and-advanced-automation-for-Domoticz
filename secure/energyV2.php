#!/usr/bin/php
<?php
require '/var/www/html/secure/functions.php';
require '/var/www/vendor/autoload.php';

use Ratchet\Client\Connector as WsConnector;
use React\EventLoop\Factory as LoopFactory;
use React\Socket\Connector as ReactConnector;

$token_energy = '005BF7516EE3E996142F7E742823275E';
$token_zon    = '0017DB17E4AC2ADC97E5279AE4142F8D';

$ip_energy = '192.168.2.4';
$ip_zon    = '192.168.2.9';

$prevtotal = 0;
$prevavg   = 0;
$prevzon   = 0;

$dbverbruik = new mysqli('192.168.2.20','home','H0m€','verbruik');
if($dbverbruik->connect_errno>0){die('Unable to connect to database ['.$dbverbruik->connect_error.']');}

// Kwartierpiek ophalen
$query="SELECT MAX(wH) AS wH FROM `kwartierpiek` WHERE date LIKE '".date('Y-m')."-%';";
if(!$result=$dbverbruik->query($query)) echo('Error query: '.$dbverbruik->error);
while($row=$result->fetch_assoc()) $kwartierpiek=$row['wH']; 
$result->free();
if (!isset($kwartierpiek)) $kwartierpiek=2500;

echo 'Kwartierpiek = '.$kwartierpiek.PHP_EOL;

// React loop
$loop = LoopFactory::create();
$reactConnector = new ReactConnector($loop, [
    'tls' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    ]
]);
$wsConnector = new WsConnector($loop, $reactConnector);

// Heartbeat functie
function wsHeartbeat($conn, $loop){
    $loop->addPeriodicTimer(25, function() use ($conn){
        if($conn) $conn->send(json_encode(['type'=>'ping']));
    });
}

// Energy websocket
$wsConnector("wss://$ip_energy/api/ws?token=$token_energy")->then(
    function($conn) use (&$prevtotal,&$prevavg,&$kwartierpiek,&$dbverbruik,&$prevzon,&$loop) {
        echo "Connected energy WS\n";

        wsHeartbeat($conn, $loop); // Start heartbeat

        $conn->on('message', function($msg) use ($conn,&$prevtotal,&$prevavg,&$kwartierpiek,&$dbverbruik,&$prevzon) {
            $data = json_decode($msg);
            if(!isset($data->total_power_import_kwh)) return;

            // mset equivalent
            $en['net'] = $data->active_power_w;
            $en['avg'] = $data->active_power_average_w;
            $en['zon'] = $prevzon;
            mset('en',$en);

            $total = (int)(($data->total_power_import_kwh*100)+($data->total_power_export_kwh*100)+($data->total_gas_m3*1000));

            if ($data->active_power_w>8500) alert('Power', 'Power usage: '.$data->active_power_w.' W!', 600, false);

            // Altijd-on logica
            $newavg = $data->active_power_average_w;
            if($prevavg>2500){
                if($newavg>$kwartierpiek-200) alert('Kwartierpiek','Kwartierpiek momenteel al '.$newavg.' Wh! Piek deze maand='.$kwartierpiek.' wH',120,false);
                if($newavg<$prevavg && $prevavg>2500){
                    $time=time();
                    $query="INSERT INTO `kwartierpiek` (`date`,`wh`) VALUES ('".date('Y-m-d H:i:s')."','".$prevavg."')";
                    if(!$dbverbruik->query($query)) echo('Error kwartierpiek insert: '.$dbverbruik->error);
                    if($prevavg>$kwartierpiek-200){
                        alert('KwartierpiekB','Kwartierpiek = '.$prevavg.' Wh Piek deze maand='.$kwartierpiek.' wH',30,false);
                        $kwartierpiek=$prevavg;
                    }
                }
            }
            $prevavg=$newavg;

            // Altijd-on device
            $power=$data->active_power_w;
            $alwayson=mget('alwayson');
            if ($power>=50&&($power<$alwayson||empty($alwayson))) {
                $db=dbconnect();
                mset('alwayson',$power);
                $time=time();
                $db->query("UPDATE devices SET icon=$power,t=$time WHERE n='elvandaag';");
                lg('New alwayson '.$power.' W');
                $vandaag=date("Y-m-d",$time);
                $query="INSERT INTO `alwayson` (`date`,`w`) VALUES ('$vandaag','$alwayson') ON DUPLICATE KEY UPDATE `w`='$alwayson'";
                if(!$dbverbruik->query($query)) echo('Error alwayson insert: '.$dbverbruik->error);
            }

            // Gas, water, verbruik updates
            foreach ($data->external as $i) {
                if ($i->type=='gas_meter') $gas=$i->value;
                elseif ($i->type=='water_meter'){
                    $prevwater=mget('water_meter');
                    $water=$i->value;
                    if ($prevwater!=$water&&mget('weg')>2) {
                        mset('water_meter',$water);
                        alert('water_meter', 'Water verbruik gedetecteerd!', 300, true);
                        lg('Waterteller: prev='.$prevwater.', nu='.$water);
                    }
                }
            }

            // Totals
            $elec=$data->total_power_import_kwh;
            $injectie=$data->total_power_export_kwh;

            $vandaag=date("Y-m-d",time());
            $dbzonphp=new mysqli('192.168.2.20','home','H0m€','egregius_zonphp');
            if($dbzonphp->connect_errno>0){die('Unable to connect to database ['.$dbzonphp->connect_error.']');}

            $query="SELECT SUM(Geg_Maand) AS Geg_Maand FROM `tgeg_maand`;";
            if(!$result=$dbzonphp->query($query)) echo('Error tgeg_maand query: '.$dbzonphp->error);
            while($row=$result->fetch_assoc()) $zontotaal=$row['Geg_Maand'];
            $result->free();

            $verbruik=round($zontotaal-$injectie+$elec,3);

            // Insert Guy and Guydag
            $query="INSERT INTO `Guy` (`date`,`gas`,`elec`,`injectie`,`zon`,`water`) VALUES ('$vandaag','$gas','$elec','$injectie','$zontotaal','$water') ON DUPLICATE KEY UPDATE `gas`='$gas',`elec`='$elec',`injectie`='$injectie',`zon`='$zontotaal',`water`='$water'";
            if(!$dbverbruik->query($query)) echo('Error Guy insert: '.$dbverbruik->error);

            $query="INSERT INTO `Guydag` (`date`,`gas`,`elec`,`verbruik`,`zon`,`water`) VALUES ('$vandaag','$gas','$elec','$verbruik','$zontotaal','$water') ON DUPLICATE KEY UPDATE `gas`='$gas',`elec`='$elec',`verbruik`='$verbruik',`zon`='$zontotaal',`water`='$water'";
            if(!$dbverbruik->query($query)) echo('Error Guydag insert: '.$dbverbruik->error);

            // Update Domoticz devices
            global $dbdomoticz,$dbuser,$dbpass,$dbname;
            if (!isset($dbdomoticz)||!isPDOConnectionAlive($dbdomoticz)) {
                $dbdomoticz=new PDO("mysql:host=127.0.0.1;dbname=$dbname;",$dbuser,$dbpass);
            }
            $stmt=$dbdomoticz->query("SELECT n,s,m FROM devices WHERE n IN ('watervandaag','gasvandaag','zonvandaag','elvandaag');");
            while($row=$stmt->fetch(PDO::FETCH_ASSOC)) $d[$row['n']]=$row;
            $water_m=$water*1000;
            if($verbruik>=10) $verbruik=round($verbruik,1); elseif($verbruik>=2) $verbruik=round($verbruik,2); else $verbruik=round($verbruik,3);
            if($zontotaal>=10) $zontotaal=round($zontotaal,1); elseif($zontotaal>=2) $zontotaal=round($zontotaal,2); else $zontotaal=round($zontotaal,3);

            $dbdomoticz->query("UPDATE devices SET s=$water_m,t=".time()." WHERE n='watervandaag';");
            $dbdomoticz->query("UPDATE devices SET s=$gas,t=".time()." WHERE n='gasvandaag';");
            $dbdomoticz->query("UPDATE devices SET s=$zontotaal,t=".time()." WHERE n='zonvandaag';");
            $dbdomoticz->query("UPDATE devices SET s=$verbruik,t=".time()." WHERE n='elvandaag';");
        });

        $conn->on('close', function($code = null, $reason = null) {
            echo "Energy WS closed: {$code} - {$reason}\n";
        });
    },
    function($e){ echo "Could not connect energy WS: {$e->getMessage()}\n"; }
);

// Solar websocket
$wsConnector("wss://$ip_zon/api/ws?token=$token_zon")->then(
    function($conn) use (&$prevzon,&$loop) {
        echo "Connected solar WS\n";

        wsHeartbeat($conn, $loop); // Start heartbeat

        $conn->on('message', function($msg) use (&$prevzon) {
            $zon=json_decode($msg);
            if(isset($zon->active_power_w)){
                $prevzon=round($zon->active_power_w);
            }
        });
        $conn->on('close', function($code = null, $reason = null){
            echo "Solar WS closed: {$code} - {$reason}\n";
        });
    },
    function($e){ echo "Could not connect solar WS: {$e->getMessage()}\n"; }
);

$loop->run();

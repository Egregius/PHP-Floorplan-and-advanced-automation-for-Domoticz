#!/usr/bin/php
<?php
require '/var/www/html/secure/functions.php';
require '/var/www/vendor/autoload.php';

use Ratchet\Client\Connector as WsConnector;
use React\EventLoop\Factory as LoopFactory;
use React\Socket\Connector as ReactConnector;

// --- Tokens ---
$token_energy = '005BF7516EE3E996142F7E742823275E';
$token_zon    = '0017DB17E4AC2ADC97E5279AE4142F8D';

// --- Previous values ---
$prevtotal = 0;
$prevavg   = 0;
$prevzon   = 0;

// --- Database connecties ---
$dbverbruik = new mysqli('192.168.2.20','home','H0m€','verbruik');
if($dbverbruik->connect_errno>0){die('Unable to connect to database ['.$dbverbruik->connect_error.']');}

$dbzonphp = new mysqli('192.168.2.20','home','H0m€','egregius_zonphp');
if($dbzonphp->connect_errno>0){die('Unable to connect to database ['.$dbzonphp->connect_error.']');}

// --- Kwartierpiek ---
$query="SELECT MAX(wH) AS wH FROM `kwartierpiek` WHERE date LIKE '".date('Y-m')."-%';";
if(!$result=$dbverbruik->query($query)) echo('Error query: '.$dbverbruik->error);
while($row=$result->fetch_assoc()) $kwartierpiek=$row['wH']; 
$result->free();
if (!isset($kwartierpiek)) $kwartierpiek=2500;
echo "[".date('Y-m-d H:i:s')."] Kwartierpiek = $kwartierpiek\n";

// --- React Loop ---
$loop = LoopFactory::create();
$reactConnector = new ReactConnector($loop, [
    'tls' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    ]
]);
$wsConnector = new WsConnector($loop, $reactConnector);

// --- Heartbeat functie ---
function wsHeartbeat($conn, $loop){
    $loop->addPeriodicTimer(25, function() use ($conn){
        echo "[".date('Y-m-d H:i:s')."] [Heartbeat] Sending ping\n";
        if($conn) $conn->send(json_encode(['type'=>'ping']));
    });
}

// --- Energy WS ---
$wsConnector("wss://p1dongle/api/ws?token=$token_energy")->then(
    function($conn) use (&$prevtotal,&$prevavg,&$kwartierpiek,&$dbverbruik,&$prevzon,&$loop) {
        echo "[".date('Y-m-d H:i:s')."] Connected energy WS\n";
        wsHeartbeat($conn, $loop);

        $conn->on('message', function($msg) use (&$prevtotal,&$prevavg,&$kwartierpiek,&$dbverbruik,&$prevzon){
            echo "[".date('Y-m-d H:i:s')."] === Energy WS Message ===\n";
            $data=json_decode($msg);
            print_r($data);

            if(!isset($data->total_power_import_kwh)) return;

            // --- mset debug ---
            echo "[".date('Y-m-d H:i:s')."] Previous mset: ";
            print_r(mget('en'));

            $newzon = $prevzon;
            mset('en', [
                'net'=>$data->active_power_w,
                'avg'=>$data->active_power_average_w,
                'zon'=>$newzon
            ]);

            echo "[".date('Y-m-d H:i:s')."] Updated mset: ";
            print_r(mget('en'));

            $total=(int)(($data->total_power_import_kwh*100)+($data->total_power_export_kwh*100)+($data->total_gas_m3*1000));

            if ($data->active_power_w>8500) alert('Power', 'Power usage: '.$data->active_power_w.' W!', 600, false);

            // --- Kwartierpiek check ---
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

            // --- Always-on check ---
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
        });

        $conn->on('close', function($code = null, $reason = null) {
            echo "[".date('Y-m-d H:i:s')."] Energy WS closed: {$code} - {$reason}\n";
        });
    },
    function($e){ echo "[".date('Y-m-d H:i:s')."] Could not connect energy WS: {$e->getMessage()}\n"; }
);

// --- Solar WS ---
$wsConnector("wss://energymeter/api/ws?token=$token_zon")->then(
    function($conn) use (&$prevzon,&$loop) {
        echo "[".date('Y-m-d H:i:s')."] Connected solar WS\n";
        wsHeartbeat($conn, $loop);

        $conn->on('message', function($msg) use (&$prevzon){
            echo "[".date('Y-m-d H:i:s')."] === Solar WS Message ===\n";
            $zon=json_decode($msg);
            print_r($zon);

            if(isset($zon->active_power_w)){
                $prevzon=round($zon->active_power_w);
            }
        });

        $conn->on('close', function($code = null, $reason = null){
            echo "[".date('Y-m-d H:i:s')."] Solar WS closed: {$code} - {$reason}\n";
        });
    },
    function($e){ echo "[".date('Y-m-d H:i:s')."] Could not connect solar WS: {$e->getMessage()}\n"; }
);

// --- Periodic loop voor Domoticz en fallback curl (1s) ---
$loop->addPeriodicTimer(1, function() use (&$prevtotal,&$prevavg,&$prevzon,&$dbverbruik,&$dbzonphp){
    // Curl fallback om data te updaten als WS niet alles stuurt
    $data=curl('http://192.168.2.4/api/v1/data');
    $data=json_decode($data);
    if(!isset($data->total_power_import_kwh)) return;

//    $prevzon=mget('en')['zon'] ?? 0;

    mset('en', [
        'net'=>$data->active_power_w,
        'avg'=>$data->active_power_average_w,
        'zon'=>$prevzon
    ]);

    // Echo met timestamp
    echo "[".date('Y-m-d H:i:s')."] Curl fallback: net={$data->active_power_w}, avg={$data->active_power_average_w}, zon=$prevzon\n";

    $total=(int)(($data->total_power_import_kwh*100)+($data->total_power_export_kwh*100)+($data->total_gas_m3*1000));

    if($total!=$prevtotal) {
        // --- Domoticz update ---
        if (!isset($dbdomoticz)||!isPDOConnectionAlive($dbdomoticz)) {
            global $dbuser,$dbpass,$dbname;
            $dbdomoticz=new PDO("mysql:host=127.0.0.1;dbname=$dbname;",$dbuser,$dbpass);
        }
        $stmt=$dbdomoticz->query("SELECT n,s,m FROM devices WHERE n IN ('watervandaag','gasvandaag','zonvandaag','elvandaag');");
        while($row=$stmt->fetch(PDO::FETCH_ASSOC)) $d[$row['n']]=$row;

        $dbdomoticz->query("UPDATE devices SET s=".($prevzon*1000).", t=".time()." WHERE n='zonvandaag';");

        $prevtotal=$total;
    }
});

$loop->run();

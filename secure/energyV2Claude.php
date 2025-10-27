#!/usr/bin/php
<?php
require '/var/www/html/secure/functions.php';
require '/var/www/vendor/autoload.php';

use Ratchet\Client\Connector as WsConnector;
use React\EventLoop\Factory as LoopFactory;
use React\Socket\Connector as ReactConnector;

// Logging functie met timestamp
function logMsg($msg) {
    echo '[' . date('Y-m-d H:i:s') . '] ' . $msg . PHP_EOL;
}

// Tokens
$token_energy = '005BF7516EE3E996142F7E742823275E';
$token_zon    = '0017DB17E4AC2ADC97E5279AE4142F8D';

// WebSocket URLs
$ws_energy = "wss://p1dongle/api/ws?token=$token_energy";
$ws_zon    = "wss://energymeter/api/ws?token=$token_zon";

// Globale variabelen
$prevtotal = 0;
$prevavg = 0;
$kwartierpiek = 2500;
$x = 1;
$lastProcessTime = 0;

// Data storage
$energyData = null;
$zonData = null;
$energyConn = null;
$zonConn = null;

// Database connectie
$dbverbruik = new mysqli('192.168.2.20', 'home', 'H0m€', 'verbruik');
if ($dbverbruik->connect_errno > 0) {
    die('Unable to connect to database [' . $dbverbruik->connect_error . ']');
}

// Haal initiële kwartierpiek op
$query = "SELECT MAX(wH) AS wH FROM `kwartierpiek` WHERE date LIKE '" . date('Y-m') . "-%';";
if (!$result = $dbverbruik->query($query)) {
    logMsg('ERROR: Query failed: ' . $query . ' - ' . $dbverbruik->error);
}
while ($row = $result->fetch_assoc()) {
    $kwartierpiek = $row['wH'];
}
$result->free();
if (!isset($kwartierpiek)) $kwartierpiek = 2500;
logMsg('Kwartierpiek = ' . $kwartierpiek);

// Event loop
$loop = LoopFactory::create();

// WebSocket connector
$reactConnector = new ReactConnector($loop, [
    'tls' => [
        'verify_peer' => false,
        'verify_peer_name' => false
    ],
    'timeout' => 10
]);
$connector = new WsConnector($loop, $reactConnector);

// Functie om data te verwerken
function processData() {
    global $energyData, $zonData, $prevtotal, $prevavg, $kwartierpiek, $x, $lastProcessTime;
    global $dbverbruik, $dbzonphp, $dbdomoticz, $dbname, $dbuser, $dbpass;
    
    // Throttle: maximum 1x per seconde
    $now = microtime(true);
    if ($now - $lastProcessTime < 1) {
        return;
    }
    $lastProcessTime = $now;
    
    if (!$energyData) {
        logMsg('WARNING: No energy data available yet');
        return;
    }
    
    logMsg('Processing data iteration #' . $x);
    
    $dag = mget('dag');
    logMsg('dag=' . $dag);
    
    $data = $energyData;
    
    // Zon data verwerken - gebruik direct zonData ipv mget('zon')
    $newzon = 0;
    if ($dag > 0 && $zonData && isset($zonData->active_power_w)) {
        $newzon = round($zonData->active_power_w);
        logMsg('Zon: active_power_w=' . $newzon . ' W');
    } else {
        logMsg('Zon: geen data (dag=' . $dag . ', zonData=' . (is_null($zonData) ? 'null' : 'set') . ')');
    }
    
    if (isset($data->total_power_import_kwh)) {
        logMsg('Energy data: net=' . $data->active_power_w . 'W, avg=' . $data->active_power_average_w . 'W');
        
        $enData = array(
            'net' => $data->active_power_w,
            'avg' => $data->active_power_average_w,
            'zon' => $newzon,
        );
        logMsg('Setting mset(en): ' . json_encode($enData));
        mset('en', $enData);
        
        // Verifieer of mset werkte
        $checkEn = mget('en');
        logMsg('Verify mget(en): ' . json_encode($checkEn));
        
        $total = (int)(($data->total_power_import_kwh * 100) + 
                      ($data->total_power_export_kwh * 100) + 
                      ($data->total_gas_m3 * 1000));
        logMsg('Total calculated: ' . $total);
        
        if ($data->active_power_w > 8500) {
            logMsg('ALERT: High power usage: ' . $data->active_power_w . ' W');
            alert('Power', 'Power usage: ' . $data->active_power_w . ' W!', 600, false);
        }
        
        // Always-on detectie
        if ($newzon == 0) {
            $power = $data->active_power_w;
            $alwayson = mget('alwayson');
            logMsg('Always-on check: power=' . $power . ', current_alwayson=' . $alwayson);
            
            if ($power >= 50 && ($power < $alwayson || empty($alwayson))) {
                logMsg('New always-on detected: ' . $power . ' W');
                $db = dbconnect();
                mset('alwayson', $power);
                $time = time();
                $db->query("UPDATE devices SET icon=$power, t=$time WHERE n='elvandaag';");
                lg('New alwayson ' . $power . ' W');
                $vandaag = date("Y-m-d", $time);
                
                if (!isset($dbverbruik) || !mysqli_ping($dbverbruik)) {
                    logMsg('Reconnecting to dbverbruik');
                    $dbverbruik = new mysqli('192.168.2.20', 'home', 'H0m€', 'verbruik');
                    if ($dbverbruik->connect_errno > 0) {
                        logMsg('ERROR: Cannot connect to database: ' . $dbverbruik->connect_error);
                        return;
                    }
                }
                $query = "INSERT INTO `alwayson` (`date`,`w`) VALUES ('$vandaag','$power') 
                         ON DUPLICATE KEY UPDATE `w`='$power'";
                if (!$dbverbruik->query($query)) {
                    logMsg('ERROR: Query failed: ' . $query . ' - ' . $dbverbruik->error);
                }
            }
        }
        
        // Kwartierpiek controle
        $newavg = $data->active_power_average_w;
        if ($prevavg > 2500) {
            logMsg('Kwartierpiek monitoring: newavg=' . $newavg . ', prevavg=' . $prevavg . ', peak=' . $kwartierpiek);
            
            if ($newavg > $kwartierpiek - 200) {
                logMsg('ALERT: Approaching kwartierpiek!');
                alert('Kwartierpiek', 'Kwartierpiek momenteel al ' . $newavg . ' Wh!' . PHP_EOL . PHP_EOL . 
                     'Piek deze maand = ' . $kwartierpiek . ' wH', 120, false);
            }
            
            if ($newavg < $prevavg && $prevavg > 2500) {
                logMsg('New quarter detected, saving peak: ' . $prevavg);
                
                if (!isset($dbverbruik) || !mysqli_ping($dbverbruik)) {
                    logMsg('Reconnecting to dbverbruik');
                    $dbverbruik = new mysqli('192.168.2.20', 'home', 'H0m€', 'verbruik');
                    if ($dbverbruik->connect_errno > 0) {
                        logMsg('ERROR: Cannot connect to database: ' . $dbverbruik->connect_error);
                        return;
                    }
                }
                
                $query = "INSERT INTO `kwartierpiek` (`date`,`wh`) 
                         VALUES ('" . date('Y-m-d H:i:s') . "','" . $prevavg . "')";
                if (!$dbverbruik->query($query)) {
                    logMsg('ERROR: Query failed: ' . $query . ' - ' . $dbverbruik->error);
                } else {
                    logMsg('Kwartierpiek saved to database');
                }
                
                if ($prevavg > $kwartierpiek - 200) {
                    logMsg('ALERT: New monthly peak: ' . $prevavg);
                    alert('KartierpiekB', 'Kwartierpiek = ' . $prevavg . ' Wh' . PHP_EOL . PHP_EOL . 
                         'Piek deze maand = ' . $kwartierpiek . ' wH', 30, false);
                    $kwartierpiek = $prevavg;
                }
            }
        }
        $prevavg = $newavg;
        
        // Database updates
        if ($total != $prevtotal) {
            logMsg('Total changed: ' . $prevtotal . ' -> ' . $total . ', updating database');
            updateVerbruikDatabase($data, $newzon);
            $prevtotal = $total;
        } else {
            logMsg('Total unchanged: ' . $total);
        }
    } else {
        logMsg('WARNING: No total_power_import_kwh in data');
    }
    
    $x++;
}

// Functie voor verbruik database update
function updateVerbruikDatabase($data, $newzon) {
    global $dbverbruik, $dbzonphp, $dbdomoticz, $dbname, $dbuser, $dbpass;
    
    logMsg('=== Starting updateVerbruikDatabase ===');
    
    $elec = $data->total_power_import_kwh;
    $injectie = $data->total_power_export_kwh;
    $gas = 0;
    $water = 0;
    
    logMsg('Raw values: elec=' . $elec . ', injectie=' . $injectie);
    
    if (isset($data->external)) {
        foreach ($data->external as $i) {
            if ($i->type == 'gas_meter') {
                $gas = $i->value;
                logMsg('Gas meter: ' . $gas);
            } elseif ($i->type == 'water_meter') {
                $prevwater = mget('water_meter');
                $water = $i->value;
                logMsg('Water meter: prev=' . $prevwater . ', new=' . $water);
                
                if ($prevwater != $water && mget('weg') > 2) {
                    mset('water_meter', $water);
                    alert('water_meter', 'Water verbruik gedetecteerd!', 300, true);
                    lg('Waterteller: prev=' . $prevwater . ', nu=' . $water);
                    logMsg('Water usage detected and alert sent');
                }
            }
        }
    }
    
    $time = time();
    $vandaag = date("Y-m-d", $time);
    
    // Zondata ophalen
    if (!isset($dbzonphp) || !mysqli_ping($dbzonphp)) {
        logMsg('Connecting to dbzonphp');
        $dbzonphp = new mysqli('192.168.2.20', 'home', 'H0m€', 'egregius_zonphp');
        if ($dbzonphp->connect_errno > 0) {
            logMsg('ERROR: Cannot connect to dbzonphp: ' . $dbzonphp->connect_error);
            return;
        }
    }
    
    $query = "SELECT Geg_Maand FROM `tgeg_maand` WHERE `Datum_Maand` = '$vandaag  0:00:00';";
    if (!$result = $dbzonphp->query($query)) {
        logMsg('ERROR: Query failed: ' . $query . ' - ' . $dbzonphp->error);
        return;
    }
    $zonvandaag = 0;
    while ($row = $result->fetch_assoc()) {
        $zonvandaag = $row['Geg_Maand'];
    }
    $result->free();
    logMsg('Zon vandaag: ' . $zonvandaag);
    
    $query = "SELECT SUM(Geg_Maand) AS Geg_Maand FROM `tgeg_maand`;";
    if (!$result = $dbzonphp->query($query)) {
        logMsg('ERROR: Query failed: ' . $query . ' - ' . $dbzonphp->error);
        return;
    }
    $zontotaal = 0;
    while ($row = $result->fetch_assoc()) {
        $zontotaal = $row['Geg_Maand'];
    }
    $result->free();
    logMsg('Zon totaal: ' . $zontotaal);
    
    // Verbruik database update
    if (!isset($dbverbruik) || !mysqli_ping($dbverbruik)) {
        logMsg('Reconnecting to dbverbruik');
        $dbverbruik = new mysqli('192.168.2.20', 'home', 'H0m€', 'verbruik');
        if ($dbverbruik->connect_errno > 0) {
            logMsg('ERROR: Cannot connect to dbverbruik: ' . $dbverbruik->connect_error);
            return;
        }
    }
    
    $query = "INSERT INTO `Guy` (`date`,`gas`,`elec`,`injectie`,`zon`,`water`) 
             VALUES ('$vandaag','$gas','$elec','$injectie','$zontotaal','$water') 
             ON DUPLICATE KEY UPDATE `gas`='$gas',`elec`='$elec',`injectie`='$injectie',
             `zon`='$zontotaal',`water`='$water'";
    if (!$dbverbruik->query($query)) {
        logMsg('ERROR: Query failed: ' . $query . ' - ' . $dbverbruik->error);
        return;
    }
    logMsg('Guy table updated');
    
    $query = "SELECT `date`,`gas`,`elec`,`injectie`,`water` FROM `Guy` ORDER BY `date` DESC LIMIT 1,1";
    if (!$result = $dbverbruik->query($query)) {
        logMsg('ERROR: Query failed: ' . $query . ' - ' . $dbverbruik->error);
        return;
    }
    $gisteren = null;
    while ($row = $result->fetch_assoc()) {
        $gisteren = $row;
    }
    $result->free();
    
    if ($gisteren) {
        logMsg('Yesterday data retrieved: ' . json_encode($gisteren));
        
        $gas = round($gas - $gisteren['gas'], 3);
        $elec = $elec - $gisteren['elec'];
        $water = round($water - $gisteren['water'], 3);
        $injectie = round($injectie - $gisteren['injectie'], 3);
        $verbruik = round($zonvandaag - $injectie + $elec, 3);
        
        logMsg('Calculated today: gas=' . $gas . ', elec=' . $elec . ', water=' . $water . 
               ', injectie=' . $injectie . ', verbruik=' . $verbruik);
        
        $query = "INSERT INTO `Guydag` (`date`,`gas`,`elec`,`verbruik`,`zon`,`water`) 
                 VALUES ('$vandaag','$gas','$elec','$verbruik','$zonvandaag','$water') 
                 ON DUPLICATE KEY UPDATE `gas`='$gas',`elec`='$elec',`verbruik`='$verbruik',
                 `zon`='$zonvandaag',`water`='$water'";
        if (!$dbverbruik->query($query)) {
            logMsg('ERROR: Query failed: ' . $query . ' - ' . $dbverbruik->error);
            return;
        }
        logMsg('Guydag table updated');
        
        // Domoticz update
        if (!isset($dbdomoticz) || !isPDOConnectionAlive($dbdomoticz)) {
            logMsg('Connecting to Domoticz database');
            $dbdomoticz = new PDO("mysql:host=127.0.0.1;dbname=$dbname;", $dbuser, $dbpass);
        }
        
        $stmt = $dbdomoticz->query("SELECT n,s,m FROM devices WHERE n IN ('watervandaag','gasvandaag','zonvandaag','elvandaag');");
        $d = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $d[$row['n']]['s'] = $row['s'];
            $d[$row['n']]['m'] = $row['m'];
        }
        logMsg('Current Domoticz values: ' . json_encode($d));
        
        $water = $water * 1000;
        if ($verbruik >= 10) $verbruik = round($verbruik, 1);
        elseif ($verbruik >= 2) $verbruik = round($verbruik, 2);
        else $verbruik = round($verbruik, 3);
        
        if ($zonvandaag >= 10) $zonvandaag = round($zonvandaag, 1);
        elseif ($zonvandaag >= 2) $zonvandaag = round($zonvandaag, 2);
        else $zonvandaag = round($zonvandaag, 3);
        
        $updated = [];
        if ($water != $d['watervandaag']['s']) {
            $dbdomoticz->query("UPDATE devices SET s=$water, t=$time WHERE n='watervandaag';");
            $updated[] = 'water';
        }
        if ($gas != $d['gasvandaag']['s']) {
            $dbdomoticz->query("UPDATE devices SET s=$gas, t=$time WHERE n='gasvandaag';");
            $updated[] = 'gas';
        }
        if ($zonvandaag != $d['zonvandaag']['s']) {
            $dbdomoticz->query("UPDATE devices SET s=$zonvandaag, t=$time WHERE n='zonvandaag';");
            $updated[] = 'zon';
        }
        if ($verbruik != $d['elvandaag']['s']) {
            $dbdomoticz->query("UPDATE devices SET s=$verbruik, t=$time WHERE n='elvandaag';");
            $updated[] = 'verbruik';
        }
        
        if (count($updated) > 0) {
            logMsg('Domoticz devices updated: ' . implode(', ', $updated));
        } else {
            logMsg('No Domoticz updates needed');
        }
    } else {
        logMsg('WARNING: No yesterday data found');
    }
    
    logMsg('=== Finished updateVerbruikDatabase ===');
}

// Dagelijkse update (00:01)
$loop->addPeriodicTimer(60, function() use (&$dbverbruik, &$dbzonphp, &$dbdomoticz, $dbname, $dbuser, $dbpass) {
    $uur = date('G');
    $min = date('i');
    
    if ($uur == 0 && $min == 1) {
        logMsg('=== Starting daily update (00:01) ===');
        $time = time();
        
        if (!isset($dbverbruik) || !mysqli_ping($dbverbruik)) {
            logMsg('Reconnecting to dbverbruik');
            $dbverbruik = new mysqli('192.168.2.20', 'home', 'H0m€', 'verbruik');
            if ($dbverbruik->connect_errno > 0) {
                logMsg('ERROR: Cannot connect to database: ' . $dbverbruik->connect_error);
                return;
            }
        }
        
        $since = date("Y-m-d", $time - (86400 * 30));
        $query = "SELECT AVG(gas) AS gas, AVG(elec) AS elec, AVG(water)*1000 AS water 
                 FROM `Guydag` WHERE date>'$since'";
        logMsg('Query: ' . $query);
        
        if (!$result = $dbverbruik->query($query)) {
            logMsg('ERROR: Query failed: ' . $query . ' - ' . $dbverbruik->error);
            return;
        }
        $avg = null;
        while ($row = $result->fetch_assoc()) {
            $avg = $row;
        }
        $result->free();
        
        if (isset($avg)) {
            logMsg('30-day averages: ' . json_encode($avg));
            
            if (!isset($dbdomoticz) || !isPDOConnectionAlive($dbdomoticz)) {
                logMsg('Connecting to Domoticz database');
                $dbdomoticz = new PDO("mysql:host=127.0.0.1;dbname=$dbname;", $dbuser, $dbpass);
            }
            $dbdomoticz->query("UPDATE devices SET m=" . round($avg['water'], 3) . " WHERE n='watervandaag';");
            $dbdomoticz->query("UPDATE devices SET m=" . round($avg['gas'], 3) . " WHERE n='gasvandaag';");
            $dbdomoticz->query("UPDATE devices SET m=" . round($avg['elec'], 3) . " WHERE n='elvandaag';");
            logMsg('Domoticz averages updated');
        }
        
        // Zon referentiedata
        if (!isset($dbzonphp) || !mysqli_ping($dbzonphp)) {
            logMsg('Connecting to dbzonphp');
            $dbzonphp = new mysqli('192.168.2.20', 'home', 'H0m€', 'egregius_zonphp');
            if ($dbzonphp->connect_errno > 0) {
                logMsg('ERROR: Cannot connect to dbzonphp: ' . $dbzonphp->connect_error);
                return;
            }
        }
        
        $maand = date('m');
        $query = "SELECT Dag_Refer FROM `tgeg_refer` WHERE Datum_Refer='2009-" . $maand . "-01 00:00:00'";
        if (!$result = $dbzonphp->query($query)) {
            logMsg('ERROR: Query failed: ' . $query . ' - ' . $dbzonphp->error);
            return;
        }
        $zonref = 0;
        while ($row = $result->fetch_assoc()) {
            $zonref = round($row['Dag_Refer'], 1);
        }
        $result->free();
        logMsg('Zon reference: ' . $zonref);
        
        $query = "SELECT AVG(Geg_Dag) AS AVG FROM `tgeg_dag` 
                 WHERE Datum_Dag LIKE '%-" . $maand . "-%' 
                 AND Geg_Dag > (SELECT MAX(Geg_Dag)/2 FROM tgeg_dag WHERE Datum_Dag LIKE '%-" . $maand . "-%')";
        if (!$result = $dbzonphp->query($query)) {
            logMsg('ERROR: Query failed: ' . $query . ' - ' . $dbzonphp->error);
            return;
        }
        $zonavg = 0;
        while ($row = $result->fetch_assoc()) {
            $zonavg = round($row['AVG'], 0);
        }
        $result->free();
        logMsg('Zon average: ' . $zonavg);
        
        if (isset($zonref, $zonavg)) {
            $dbdomoticz->query("UPDATE devices SET m=" . $zonref . ", icon=" . $zonavg . " WHERE n='zonvandaag';");
            logMsg('Zon reference data updated in Domoticz');
        }
        
        logMsg('=== Finished daily update ===');
    }
});

// Functie om WebSocket connectie te maken met automatische reconnect
function connectWebSocket($connector, $url, $name, &$connVar, &$dataVar, $loop) {
    logMsg('Attempting to connect to ' . $name . ' WebSocket: ' . $url);
    
    $connector($url)->then(
        function($conn) use ($name, &$connVar, &$dataVar) {
            logMsg('✓ Connected to ' . $name . ' WebSocket');
            $connVar = $conn;
            
            $conn->on('message', function($msg) use ($name, &$dataVar) {
                $msgStr = (string)$msg;
                $dataVar = json_decode($msgStr);
                if ($dataVar === null) {
                    logMsg('WARNING: ' . $name . ' - Invalid JSON received: ' . substr($msgStr, 0, 100));
                } else {
                    logMsg($name . ' data received: ' . strlen($msgStr) . ' bytes - ' . substr($msgStr, 0, 200));
                    
                    // Debug: toon belangrijke velden
                    if ($name === 'Energy' && isset($dataVar->total_power_import_kwh)) {
                        logMsg($name . ' - Power: ' . $dataVar->active_power_w . 'W, Import: ' . $dataVar->total_power_import_kwh . ' kWh');
                    } elseif ($name === 'Solar' && isset($dataVar->active_power_w)) {
                        logMsg($name . ' - Power: ' . $dataVar->active_power_w . 'W');
                    }
                }
                
                // Proces data wanneer we energy data ontvangen
                if ($name === 'Energy') {
                    processData();
                }
            });
            
            $conn->on('close', function($code = null, $reason = null) use ($name, &$connVar, &$dataVar) {
                logMsg('✗ ' . $name . ' connection closed (code: ' . $code . ', reason: ' . $reason . ')');
                $connVar = null;
                $dataVar = null;
                logMsg('Connection will be automatically reconnected');
            });
            
            $conn->on('error', function($e) use ($name) {
                logMsg('ERROR: ' . $name . ' - ' . $e->getMessage());
            });
        },
        function($e) use ($name) {
            logMsg('✗ Could not connect to ' . $name . ': ' . $e->getMessage());
            logMsg('Will retry in next connection attempt');
        }
    );
}

logMsg('Starting WebSocket monitoring...');

// Connecteer met energie meter WebSocket
connectWebSocket($connector, $ws_energy, 'Energy', $energyConn, $energyData, $loop);

// Connecteer met zon meter WebSocket
connectWebSocket($connector, $ws_zon, 'Solar', $zonConn, $zonData, $loop);

// Heartbeat timer om te controleren of connecties nog leven en opnieuw te verbinden indien nodig
$loop->addPeriodicTimer(30, function() use (&$energyConn, &$zonConn, $connector, $ws_energy, $ws_zon, &$energyData, &$zonData, $loop) {
    $energyStatus = is_null($energyConn) ? 'DISCONNECTED' : 'connected';
    $zonStatus = is_null($zonConn) ? 'DISCONNECTED' : 'connected';
    logMsg('Heartbeat - Energy: ' . $energyStatus . ', Solar: ' . $zonStatus);
    
    // Reconnect indien nodig
    if (is_null($energyConn)) {
        logMsg('Reconnecting Energy...');
        connectWebSocket($connector, $ws_energy, 'Energy', $energyConn, $energyData, $loop);
    }
    if (is_null($zonConn)) {
        logMsg('Reconnecting Solar...');
        connectWebSocket($connector, $ws_zon, 'Solar', $zonConn, $zonData, $loop);
    }
});

$loop->run();
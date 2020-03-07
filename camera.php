<?php
/**
 * Pass2PHP
 * php version 7.3.4-2
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
require '/var/www/config.php';
$mysqli=new mysqli('localhost', $dbuser, $dbpass, $dbname);
$result = $mysqli->query("select n,s,t from devices;") or trigger_error($mysqli->error." [$sql]");
while ($row = $result->fetch_array()) {
    $d[$row['n']]['s'] = $row['s'];
    $d[$row['n']]['t'] = $row['t'];
}
$data=array();
$data['Weg']=$d['Weg']['s'];
$data['poortrf']=$d['poortrf']['s'];
$data['deurvoordeur']=$d['deurvoordeur']['s'];
$data['tdeurvoordeur']=time()-$d['deurvoordeur']['t'];
echo serialize($data);
if ($d['Weg']['s']==0&&$d['poortrf']['s']=='Off'&&$d['deurvoordeur']['s']=='Closed') shell_exec('curl -s "http://127.0.0.1/beep.php" > /dev/null 2>/dev/null &');
<?php
/**
 * Pass2PHP
 * php version 8.0
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
require '/var/www/config.php';
$mysqli=new mysqli('localhost', $dbuser, $dbpass, $dbname);
$result = $mysqli->query("select n,s,t from devices WHERE n in ('Weg', 'poortrf', 'deurvoordeur');") or trigger_error($mysqli->error." [$sql]");
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

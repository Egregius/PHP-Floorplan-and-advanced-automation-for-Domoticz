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
$mysqli=new mysqli('localhost', 'domotica', 'domotica', 'domotica');
$result = $mysqli->query("select n,i,s,UNIX_TIMESTAMP(t) as t,m from devices;") or trigger_error($mysqli->error." [$sql]");
while ($row = $result->fetch_array()) {
    $d[$row['n']]['i'] = $row['i'];
    $d[$row['n']]['s'] = $row['s'];
    $d[$row['n']]['t'] = $row['t'];
    $d[$row['n']]['m'] = $row['m'];
}

$data=array();
$data['Weg']=$d['Weg']['s'];
$data['meldingen']=$d['auto']['s'];
$data['poortrf']=$d['poortrf']['s'];
echo serialize($data);
shell_exec('curl -s "http://127.0.0.1/beep.php" > /dev/null 2>/dev/null &');

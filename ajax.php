<?php
/**
 * Pass2PHP
 * php version 7.3.4-2
 *
 * This file gives the status of the devices changed in the last 2 seconds in json format.
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
$t=time();
$d=array();
$d['time']['n']='time';
$d['time']['s']=null;
$d['time']['t']=strftime("%k:%M:%S", $t);
$d['time']['m']=null;
$d['time']['dt']=null;
$t=$t-2;
$db=new PDO("mysql:host=localhost;dbname=domotica;", 'domotica', 'domotica');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$stmt=$db->query("SELECT n,i,s,t,m,dt FROM devices WHERE t >= $t;");
while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
    $d[$row['n']] = $row;
}
echo json_encode($d);
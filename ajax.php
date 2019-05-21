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
if (isset($_REQUEST['timestamp'])) {
    require '/var/www/config.php';
    require 'secure/authentication.php';
    if ($home==true) {
        $t=time();
        $d=array();
        $d['t']=$t;
        $d['ip']=$ipaddress;
        $t=$_REQUEST['timestamp'];
        $db=new PDO("mysql:host=localhost;dbname=domotica;", 'domotica', 'domotica');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt=$db->query("SELECT n,i,s,t,m,dt,icon FROM devices WHERE `dt` not like 'x' AND t >= $t;");
        while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
            //$d[$row['n']]['n'] = $row['n'];
            $d[$row['n']]['s'] = $row['s'];
            $d[$row['n']]['t'] = $row['t'];
            if(!empty($row['i']))$d[$row['n']]['i'] = $row['i'];
            if(!empty($row['m']))$d[$row['n']]['m'] = $row['m'];
            if(!empty($row['dt']))$d[$row['n']]['dt'] = $row['dt'];
            if(!empty($row['icon']))$d[$row['n']]['ic'] = $row['icon'];
        }
        echo json_encode($d);
    }
}
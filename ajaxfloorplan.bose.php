<?php
/**
 * Pass2PHP
 * php version 7.3.11-1
 *
 * This file gives the status of the bose soundtouch with the $_REQUEST['ip'].
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
 if (isset($_REQUEST['ip'])) {
    $bose=$_REQUEST['ip'];
    $d=array();
    $d['time']=time();
    $d['nowplaying']=json_decode(json_encode(simplexml_load_string(file_get_contents("http://192.168.2.$bose:8090/now_playing"))), true);
    $d['volume']=json_decode(json_encode(simplexml_load_string(file_get_contents("http://192.168.2.$bose:8090/volume"))), true);
    $d['bass']=json_decode(json_encode(simplexml_load_string(file_get_contents("http://192.168.2.$bose:8090/bass"))), true);
    echo json_encode($d);
 }
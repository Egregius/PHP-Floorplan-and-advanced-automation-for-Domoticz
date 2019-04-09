<?php
/**
 * Pass2PHP
 * php version 7.3.3-1
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
if ($d['denon']['s']=='On') {
    denon('MVUP');
    denon('MVUP');
    denon('MVUP');
    denon('MVUP');
    denon('MVUP');
    denon('MVUP');
} else {
    $nowplaying=@json_decode(@json_encode(@simplexml_load_string(@file_get_contents('http://192.168.2.3:8090/now_playing'))), true);
    if (!empty($nowplaying)) {
        if (isset($nowplaying['@attributes']['source'])) {
            if ($nowplaying['@attributes']['source']!='STANDBY') {
                $volume=@json_decode(@json_encode(@simplexml_load_string(@file_get_contents('http://192.168.2.3:8090/volume'))), true);
                $cv=$volume['actualvolume'];
                if ($cv>80) {
                    exit;
                }
                if ($cv>50) {
                    bosevolume($cv+5);
                } elseif ($cv>30) {
                    bosevolume($cv+4);
                } elseif ($cv>20) {
                    bosevolume($cv+3);
                } elseif ($cv>10) {
                    bosevolume($cv+2);
                } else {
                    bosevolume($cv+1);
                }
            }
        }
    }
}
/**
 * Function denon
 *
 * @param string $cmd command to sent to function denontcp
 *
 * @return null
 */
function denon($cmd)
{
    for ($x=1;$x<=10;$x++) {
        if (denontcp($cmd, $x)) {
            break;
        }
    }
}
/**
 * Function denontcp: Store values in MySQL database as cache items.
 *
 * @param string $cmd command to sent
 * @param int    $x   Multiply usleep timeout
 *
 * @return boolean
 */
function denontcp($cmd,$x)
{
    $sleep=102000*$x;
    $socket=@fsockopen("192.168.2.6", "23", $errno, $errstr, 2);
    if ($socket) {
        fputs($socket, "$cmd\r\n");
        fclose($socket);
        usleep($sleep);
        return true;
    } else {
        usleep($sleep);
        return false;
    }
}
store('Weg', 0, null, true);
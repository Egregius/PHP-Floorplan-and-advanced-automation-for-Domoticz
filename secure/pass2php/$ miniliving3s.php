<?php
/**
 * Pass2PHP
 * php version 7.3
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
if ($d['denon']['s']=='On'&&$d['denonpower']['s']=='ON') {
    denon('MVDOWN');
    denon('MVDOWN');
    denon('MVDOWN');
    denon('MVDOWN');
    denon('MVDOWN');
    denon('MVDOWN');
} elseif ($d['tv']['s']=='On'&&$d['lgtv']['s']=='On') {
	exec('sudo /var/www/html/secure/lgtv.py -c volume-down 192.168.2.27');
	exec('sudo /var/www/html/secure/lgtv.py -c volume-down 192.168.2.27');
} elseif ($d['bose101']['s']=='On') {
    $nowplaying=@json_decode(@json_encode(@simplexml_load_string(@file_get_contents('http://192.168.2.101:8090/now_playing'))), true);
    if (!empty($nowplaying)) {
        if (isset($nowplaying['@attributes']['source'])) {
            if ($nowplaying['@attributes']['source']!='STANDBY') {
                $volume=@json_decode(@json_encode(@simplexml_load_string(@file_get_contents('http://192.168.2.101:8090/volume'))), true);
                $cv=$volume['actualvolume'];
                if ($cv>50) {
                    bosevolume($cv-5);
                } elseif ($cv>30) {
                    bosevolume($cv-4);
                } elseif ($cv>20) {
                    bosevolume($cv-3);
                } elseif ($cv>10) {
                    bosevolume($cv-2);
                } else {
                    bosevolume($cv-1);
                }
            }
        }
    }
}
store('Weg', 0, basename(__FILE__).':'.__LINE__);
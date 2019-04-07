<?php
/**
 * Pass2PHP
 * php version 7.2.15
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
if ($d['keuken']['s']=='On') {
    if ($d['lgtv']['s']=='On') {
        lgcommand('play');
    }
    andereuit();
} else {
    if ($d['lgtv']['s']=='On') {
        lgcommand('pause');
    }
    if ($d['keuken']['s']=='Off') {
        sw('keuken', 'On', false);
    }
}
/**
 * Function andereuit
 *
 * Switches off unneeded devices
 *
 * @return null
 */
function andereuit()
{
    global $d;
    $items=array('pirkeuken','pirgarage','pirinkom','pirhall');
    foreach ($items as $item) {
        if ($d[$item]['s']!='Off') {
            ud($item, 0, 'Off');
        }
    }
    sw('keuken', 'Off', false);
    $items=array('eettafel','zithoek');
    foreach ($items as $item) {
        if ($d[$item]['s']>0) {
            sl($item, 0);
        }
    }
    $items=array('werkblad1','wasbak','kookplaat','garage','inkom','hall');
    foreach ($items as $item) {
        if ($d[$item]['s']!='Off') {
            sw($item, 'Off');
        }
    }
    $status=json_decode(json_encode(simplexml_load_string(file_get_contents("http://192.168.2.5:8090/now_playing"))), true);
    if (!empty($status)) {
        if (isset($status['@attributes']['source'])) {
            if ($status['@attributes']['source']!='STANDBY') {
                sw('bose3', 'Off');
                bosekey("POWER", 0, 5);
            }
        }
    }
}
store('Weg', 0, null, true);
if ($d['Xlight']['s']!='Off') {
    sw('Xlight', 'Off');
}
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
if ($d['keuken']['s']=='On') {
    if ($d['lgtv']['s']=='On') {
        shell_exec('python3 secure/lgtv.py -c play '.$lgtvip);
    }
    andereuit();
} else {
    if ($d['lgtv']['s']=='On') {
        shell_exec('python3 secure/lgtv.py -c pause '.$lgtvip);
    }
    if ($d['keuken']['s']=='Off') {
        sw('keuken', 'On', basename(__FILE__).':'.__LINE__);
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
    sw('keuken', 'Off', basename(__FILE__).':'.__LINE__);
    $items=array('eettafel','zithoek');
    foreach ($items as $item) {
        if ($d[$item]['s']>0) {
            sl($item, 0, basename(__FILE__).':'.__LINE__);
        }
    }
    $items=array('werkblad1','wasbak','kookplaat','garage','inkom','hall');
    foreach ($items as $item) {
        if ($d[$item]['s']!='Off') {
            sw($item, 'Off', basename(__FILE__).':'.__LINE__);
        }
    }
    $status=json_decode(json_encode(simplexml_load_string(file_get_contents("http://192.168.2.5:8090/now_playing"))), true);
    if (!empty($status)) {
        if (isset($status['@attributes']['source'])) {
            if ($status['@attributes']['source']!='STANDBY') {
                sw('bose3', 'Off', basename(__FILE__).':'.__LINE__);
                bosekey("POWER", 0, 5);
            }
        }
    }
}
store('Weg', 0, basename(__FILE__).':'.__LINE__);
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
if ($d['kamer']['m']==2) {
	sl('kamer', (1+$d['kamer']['s']), basename(__FILE__).':'.__LINE__);
	$volume=@json_decode(@json_encode(@simplexml_load_string(@file_get_contents('http://192.168.2.103:8090/volume'))), true);
	bosevolume((1+$volume['actualvolume']), 103);           
} elseif ($status=='On') {
    $item='RkamerR';
    if ($d[$item]['s']>85) {
        sl($item, 85, basename(__FILE__).':'.__LINE__);
    } elseif ($d[$item]['s']>70) {
        sl($item, 70, basename(__FILE__).':'.__LINE__);
    } elseif ($d[$item]['s']>40) {
        sl($item, 40, basename(__FILE__).':'.__LINE__);
    } elseif ($d[$item]['s']>0) {
        sl($item, 0, basename(__FILE__).':'.__LINE__);
    }
}
resetsecurity();
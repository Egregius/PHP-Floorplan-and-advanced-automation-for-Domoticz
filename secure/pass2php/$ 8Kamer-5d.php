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
if ($d['kamer']['m']==2) {
	sl('kamer', (1+$d['kamer']['s']), basename(__FILE__).':'.__LINE__);
	$volume=@json_decode(@json_encode(@simplexml_load_string(@file_get_contents('http://192.168.2.103:8090/volume'))), true);
	bosevolume((1+$volume['actualvolume']), 103);           
} elseif ($status=='On') {
    $item='RkamerL';
    if ($d[$item]['s']<100) sl($item, 100, basename(__FILE__).':'.__LINE__);
}
resetsecurity();
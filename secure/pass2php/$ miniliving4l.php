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
lg('script 4 L');
if ($d['tv']['s']=='Off'||$d['denon']['s']=='Off'/*||$d['nvidia']['s']=='Off'*/) {
    $items=array('tv','denon'/*,'nvidia'*/);
    foreach ($items as $item) {
        if ($d[$item]['s']!='On') {
            sw($item, 'On', basename(__FILE__).':'.__LINE__);
        }
    }
    sleep(4);
    lgcommand('on');
    for ($x=1;$x<=4;$x++) {
		lgcommand('on');
		sleep(2);
	}
} else {
    if ($d['lgtv']['s']=='On') {
        lgcommand('off');
        sleep(2);
    }
    $items=array('lgtv','bosesoundlink','kristal');
    foreach ($items as $item) {
        if ($d[$item]['s']!='Off') {
            sw($item, 'Off', basename(__FILE__).':'.__LINE__);
        }
    }
}
store('Weg', 0, basename(__FILE__).':'.__LINE__);
sleep(10);
//sw('nvidia', 'Off', basename(__FILE__).':'.__LINE__);
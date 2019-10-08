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
bosekey("POWER");
$items=array('bose102', 'bose103', 'bose104', 'bose105');
if ($d['bose101']['s']=='On') {
	sw('bose101', 'Off');
	foreach ($items as $i) {
		if ($d[$i]['s']=='On') {
			sw($i, 'Off');
		}
	}
} else {
	sw('bose101', 'On');
}
store('Weg', 0, basename(__FILE__).':'.__LINE__);
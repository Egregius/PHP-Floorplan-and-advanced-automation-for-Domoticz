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

$items=array('bose102', 'bose103', 'bose104', 'bose105');
if (($d['denon']['s']=='On'||$d['denonpower']['s']=='ON')&&$d['bose101']['s']=='On') {
	sw('bose101', 'Off');
	bosekey("POWER");
	foreach ($items as $i) {
		if ($d[$i]['s']=='On') {
			sw($i, 'Off');
			bosekey("POWER");
		}
	}
} else {
	if ($d['bose101']['s']=='Off') {
		sw('bose101', 'On');
		bosekey("POWER");
	} else {
		if ($d['bose101']['s']=='Off') {
			saytime(105);
			boseplayinfo('Het wordt vandaag tussen '.floor($d['minmaxtemp']['s']).' en '.ceil($d['minmaxtemp']['m']).' graden'.owcondition(), 30, basename(__FILE__).':'.__LINE__, 105);
			bosezone(105);
		} else {
			saytime(101);
			boseplayinfo('Het wordt vandaag tussen '.floor($d['minmaxtemp']['s']).' en '.ceil($d['minmaxtemp']['m']).' graden'.owcondition(), 30, basename(__FILE__).':'.__LINE__, 101);
		}
	}
}
store('Weg', 0, basename(__FILE__).':'.__LINE__);
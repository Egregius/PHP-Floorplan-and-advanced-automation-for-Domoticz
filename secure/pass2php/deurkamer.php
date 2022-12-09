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
if ($d['auto']['s']=='On') {
	if ($status=='Open') {
	//	$zononder=($d['civil_twilight']['m']+$d['Sun']['m'])/2;
//		if ($d['kamer']['s']<5&&$d['Weg']['s']==0&&TIME>$zononder) sl('kamer', 1, basename(__FILE__).':'.__LINE__);
		fhall();
	} else {
		if ($d['daikin']['m']==0&&$d['daikin']['s']=='On') {
			if ($d['heating']['s']<0) daikinset('kamer', 1, 3, 20, basename(__FILE__).':'.__LINE__, 'B');
			else daikinset('kamer', 1, 4, 10, basename(__FILE__).':'.__LINE__, 'B');
		}
	}
}
if ($status=='Open') sirene('Deur kamer open');
else sirene('Deur kamer dicht');

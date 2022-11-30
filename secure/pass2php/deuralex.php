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
		$zonop=($d['civil_twilight']['s']+$d['Sun']['s'])/2;
		$zononder=($d['civil_twilight']['m']+$d['Sun']['m'])/2;
//		if (($d['alex']['s']<1&&$d['zon']['s']==0&&(TIME<$zonop||TIME>$zononder))||$d['Ralex']['s']>70) sl('alex', 1, basename(__FILE__).':'.__LINE__);
		if ($d['alex']['s']<1&&(($d['zon']['s']==0&&TIME>$zononder)||$d['Ralex']['s']>80)) {
			sl('alex', 1, basename(__FILE__).':'.__LINE__);
			usleep(300000);
			sl('alex', 1, basename(__FILE__).':'.__LINE__);
		}
		fhall();
	} else {
		if ($d['daikin']['m']==0&&$d['daikin']['s']=='On') {
			if ($d['heating']['s']<0) daikinset('alex', 1, 3, 20, basename(__FILE__).':'.__LINE__, 'B', 50);
			else daikinset('alex', 1, 4, 10, basename(__FILE__).':'.__LINE__, 'B', 50);
		}
	}
}
if ($status=='Open') sirene('Deur Alex open');
else sirene('Deur Alex dicht');

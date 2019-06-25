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
if ($d['auto']['s']=='On') {
	if ($status=='Open') {
		fhall();
		if (TIME>strtotime('7:00')&&TIME<strtotime('10:00')) {
			if ($d['Ralex']>0) {
				sl('Ralex', 0, basename(__FILE__).':'.__LINE__);
				storemode('Ralex', 0, basename(__FILE__).':'.__LINE__);
			}
		}
	} elseif ($status=='Closed') {
 
	}
}
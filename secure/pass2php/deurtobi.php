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
		fhall();
		if (TIME>strtotime('6:00')&&TIME<strtotime('10:00')) {
			if ($d['Rtobi']>0) {
				sl('Rtobi', 0, basename(__FILE__).':'.__LINE__);
			}
		}
	}
}
if ($status=='Open') sirene('Deur Tobi open');
else sirene('Deur Tobi dicht');
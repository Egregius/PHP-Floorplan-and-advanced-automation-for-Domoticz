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
		if (TIME>strtotime('7:30')&&TIME<strtotime('10:00')) {
			if ($d['Ralex']>0) {
				sl('Ralex', 0, basename(__FILE__).':'.__LINE__);
			}
		}
	}
}
if ($status=='Open') sirene('Deur Alex open');
else sirene('Deur Alex dicht');
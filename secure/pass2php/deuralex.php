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
	} else {
		if ($d['daikin']['m']==0&&$d['daikin']['s']=='On') {
			if ($d['heating']['s']<0) daikinset('alex', 1, 3, 20, basename(__FILE__).':'.__LINE__, 'B');
			else daikinset('alex', 1, 4, 10, basename(__FILE__).':'.__LINE__, 'B');
		}
	}
}
if ($status=='Open') sirene('Deur Alex open');
else sirene('Deur Alex dicht');

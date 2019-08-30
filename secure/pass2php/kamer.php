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
if ($status==0) {
	if ($d['kamer']['m']!=0) {
	    storemode('kamer', 0, basename(__FILE__).':'.__LINE__);
	}
	if (TIME<strtotime('20:00')) {
		if ($d['bose103']['s']=='On') {
			bosekey('POWER', 103);
		}
	}
}
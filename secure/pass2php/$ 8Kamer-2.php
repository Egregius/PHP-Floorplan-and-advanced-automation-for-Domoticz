<?php
/**
 * Pass2PHP
 * php version 7.3
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link	 https://egregius.be
 **/
if ($status=='On') {
	$item='RkamerR';
	if ($d[$item]['s']>85) {
		sl($item, 85, basename(__FILE__).':'.__LINE__);
	} elseif ($d[$item]['s']>70) {
		sl($item, 70, basename(__FILE__).':'.__LINE__);
	} elseif ($d[$item]['s']>40) {
		sl($item, 40, basename(__FILE__).':'.__LINE__);
	} elseif ($d[$item]['s']>1) {
		sl($item, 1, basename(__FILE__).':'.__LINE__);
	}
}

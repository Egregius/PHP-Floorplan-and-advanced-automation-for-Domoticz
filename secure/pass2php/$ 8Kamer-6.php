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
$item='RkamerR';
if ($d['heating']['s']<0) {
	if ($d[$item]['s']<70) {
		sl($item, 70, basename(__FILE__).':'.__LINE__);
	} elseif ($d[$item]['s']<76) {
		sl($item, 76, basename(__FILE__).':'.__LINE__);
	} elseif ($d[$item]['s']<82) {
		sl($item, 82, basename(__FILE__).':'.__LINE__);
	}
} else {
	if ($d[$item]['s']<100) {
		sl($item, 100, basename(__FILE__).':'.__LINE__);
	}
}

resetsecurity();
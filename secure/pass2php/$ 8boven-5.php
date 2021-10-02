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
	$item='RkamerL';
	if ($d['heating']['s']>=0) {
		sl($item, 100, basename(__FILE__).':'.__LINE__);
	} else {
		$half=45;
		$lijntjes=82;
		$itemstatus=$d[$item]['s'];
		if ($itemstatus<$half) {
			sl($item, $half, basename(__FILE__).':'.__LINE__);
		} elseif ($itemstatus<$lijntjes) {
			sl($item, $lijntjes, basename(__FILE__).':'.__LINE__);
		} else {
			sl($item, 100, basename(__FILE__).':'.__LINE__);
		}
	}
	resetsecurity();
}

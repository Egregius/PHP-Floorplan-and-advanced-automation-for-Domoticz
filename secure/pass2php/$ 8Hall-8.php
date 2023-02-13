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
	sl('waskamer', 8, basename(__FILE__).':'.__LINE__);
	storemode('waskamer', 1, basename(__FILE__).':'.__LINE__);
	if ($d['Rwaskamer']['s']<70) {
		sl('Rwaskamer', 100);
	}
}

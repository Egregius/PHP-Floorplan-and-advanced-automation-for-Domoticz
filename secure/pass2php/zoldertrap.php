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
if ($status=='Open') {
	if ($d['bureelspeelkamer']['s']=='Off'&&$d['tvspeelkamer']=='Off'&&$d['zolder']['s']==0) {
		sl('zolder', 50, basename(__FILE__).':'.__LINE__);
	}
} elseif ($status=='Closed') {
	if ($d['bureelspeelkamer']['s']=='Off'&&$d['tvspeelkamer']=='Off'&&$d['zolder']['s']>0) {
		sl('zolder', 0, basename(__FILE__).':'.__LINE__);
	}
}

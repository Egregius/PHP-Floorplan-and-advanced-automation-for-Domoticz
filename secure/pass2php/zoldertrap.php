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
if ($status=='Open') {
	if ($d['bureeltobi']['s']=='Off'&&$d['tvtobi']=='Off'&&$d['zolder']['s']==0) {
		sl('zolder', 50);
	}
} elseif ($status=='Closed') {
	if ($d['bureeltobi']['s']=='Off'&&$d['tvtobi']=='Off'&&$d['zolder']['s']>0) {
		sl('zolder', 0);
	}
}
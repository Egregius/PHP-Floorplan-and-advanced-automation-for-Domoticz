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
if ($status>0) {
//	if ($d['keuken']['s']=='On') {
//		sw('keuken', 'Off',basename(__FILE__).':'.__LINE__);
//	}
//	if ($d['GroheRed']['s']=='Off') sw('GroheRed', 'On', basename(__FILE__).':'.__LINE__);
} else {
	if ($d['keuken']['s']=='Off'&&$d['GroheRed']['s']=='On'&&$d['GroheRed']['m']==0) {
		sw('GroheRed', 'Off', basename(__FILE__).':'.__LINE__);
	}
}

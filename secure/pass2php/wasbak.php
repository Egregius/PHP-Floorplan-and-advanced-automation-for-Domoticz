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
	if ($d['keuken']['s']=='On') {
		sw('keuken', 'Off',basename(__FILE__).':'.__LINE__);
	}
	if ($d['GroheRed']['s']=='Off') {
		sw('GroheRed', 'Off', basename(__FILE__).':'.__LINE__);
	}
} else {
	if ($d['zon']['s']==0&&$d['keuken']['s']=='Off'&&$d['kookplaat']['s']=='Off'&&$d['werkblad1']['s']=='Off'&&$d['auto']['s']) {
		sw('keuken', 'On',basename(__FILE__).':'.__LINE__);
	}
	if ($d['keuken']['s']=='Off'&&$d['kookplaat']['s']=='Off'&&$d['werkblad1']['s']=='Off'&&$d['GroheRed']['s']=='On'&&$d['GroheRed']['m']==0) {
		sw('GroheRed', 'Off', basename(__FILE__).':'.__LINE__);
	}
}

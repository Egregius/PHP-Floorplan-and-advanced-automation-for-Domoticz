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
		$zonop=($d['civil_twilight']['s']+$d['Sun']['s'])/2;
		$zononder=($d['civil_twilight']['m']+$d['Sun']['m'])/2;
		if (($d['speelkamer']['s']<100&&$d['zon']['s']==0&&(TIME<$zonop||TIME>$zononder))||$d['Rspeelkamer']['s']>70) sl('speelkamer', 100, basename(__FILE__).':'.__LINE__);
		fhall();
		if (TIME>strtotime('6:00')&&TIME<strtotime('10:00')&&$d['Ralex']['s']==0&&$d['Rspeelkamer']>0) sl('Rspeelkamer', 0, basename(__FILE__).':'.__LINE__);
	}
}
if ($status=='Open') sirene('Deur speelkamer open');
else sirene('Deur speelkamer dicht');

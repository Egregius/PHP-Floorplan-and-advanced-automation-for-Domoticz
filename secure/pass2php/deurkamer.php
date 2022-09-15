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
		$zononder=($d['civil_twilight']['m']+$d['Sun']['m'])/2;
		if ($d['kamer']['s']<5&&TIME>$zononder) sl('kamer', 5, basename(__FILE__).':'.__LINE__);
		fhall();
	}
}
if ($d['kamer']['m']!=0&&$d['kamer']['s']==0&&past('kamer')<90) {
	storemode('kamer', 0, basename(__FILE__).':'.__LINE__);
}
if ($status=='Open') sirene('Deur kamer open');
else sirene('Deur kamer dicht');

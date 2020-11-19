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
if ($status=="Open"&&$d['auto']['s']=='On') {
	if (TIME>=$d['civil_twilight']['s']&&TIME<=$d['civil_twilight']['m']) $dag=true; else $dag=false;
	if ($d['voordeur']['s']=='Off'&&$d['zon']['s']==0&&$dag==false) sw('voordeur', 'On', basename(__FILE__).':'.__LINE__);
	finkom();
}
if ($status=='Open') sirene('Voordeur open');
else sirene('Voordeur dicht');


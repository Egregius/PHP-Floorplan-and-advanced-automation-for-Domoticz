<?php
/**
 * Pass2PHP
 * php version 8.0
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
if ($status=="Open"&&$d['auto']['s']=='On') {
        $zonop=($d['civil_twilight']['s']+$d['Sun']['s'])/2;
	$zononder=($d['civil_twilight']['m']+$d['Sun']['m'])/2;
	if ($d['voordeur']['s']=='Off'&&$d['zon']['s']==0&&(TIME<$zonop||TIME>$zononder)) sw('voordeur', 'On', basename(__FILE__).':'.__LINE__);
	finkom();
}
if ($status=='Open') sirene('Voordeur open');
else sirene('Voordeur dicht');


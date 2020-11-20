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
	if ($d['voordeur']['s']=='Off'&&$d['zon']['s']==0&&(TIME<$d['Sun']['s']||TIME>$d['Sun']['m'])) sw('voordeur', 'On', basename(__FILE__).':'.__LINE__);
	finkom();
}
if ($status=='Open') sirene('Voordeur open');
else sirene('Voordeur dicht');


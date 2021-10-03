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
if ($d['auto']['s']=='On'&&$d['Weg']['s']==1&&TIME>strtotime('6:00')&&TIME<strtotime('12:00')) {
	store('Weg', 0, basename(__FILE__).':'.__LINE__);
}
if ($d['badkamervuur1']['s']=='On') {
	if ($d['badkamervuur2']['s']=='On') sw('badkamervuur2', 'Off');
	sw('badkamervuur1', 'Off');
}
if ($d['badkamer_set']['s']!=10) {
	store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
}
if ($d['lichtbadkamer']['s']>0) {
	sw('lichtbadkamer', 'Off', basename(__FILE__).':'.__LINE__);
}
if ($d['auto']['s']=='On') {
	fhall();
}
if ($d['badkamer_set']['m']!=0) {
	storemode('badkamer_set', 0, basename(__FILE__).':'.__LINE__);
}
douche();
if (TIME>strtotime('20:00')&&$d['Weg']['s']==1&&$d['kamer']['s']>0) {
	if ($d['kamer']['m']!=1) {
		storemode('kamer', 1, basename(__FILE__).':'.__LINE__);
	}
}
resetsecurity();

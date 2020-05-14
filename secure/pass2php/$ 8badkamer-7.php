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
if ($d['badkamervuur1']['s']=='On') {
	store('badkamer_set', 10, basename(__FILE__).':'.__LINE__);
	if ($d['badkamervuur2']['s']=='On') sw('badkamervuur2', 'Off');
	sw('badkamervuur1', 'Off');
} elseif ($d['heating']['s']>=0) {
	store('badkamer_set', 21, basename(__FILE__).':'.__LINE__);
	sw('badkamervuur1', 'On');
	if ($d['badkamervuur2']['s']=='Off') sw('badkamervuur2', 'On');
}
douche();
resetsecurity();
if ($d['bose102']['s']=='Off') bosezone(102);
if ($d['bose102']['m']==0) {
	boseplayinfo(saytime().sayweather());
	storemode('bose102', 1);
}
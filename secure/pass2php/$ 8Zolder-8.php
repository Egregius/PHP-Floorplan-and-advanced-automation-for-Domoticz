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
if ($d['zon']['s']==0) {
	sl('zolder', 8, basename(__FILE__).':'.__LINE__);
	storemode('zolder', 1, basename(__FILE__).':'.__LINE__);
} else {
	sl('zolder', 0, basename(__FILE__).':'.__LINE__);
}
sw('bureeltobi', 'Off');
sw('tvtobi', 'Off');

if ($d['raamhall']['s']=='Closed') {
    sw('zoldertrap', 'On', basename(__FILE__).':'.__LINE__);
    if ($d['Weg']['s']>0) {
    	store('Weg', 0, basename(__FILE__).':'.__LINE__);
    }
}
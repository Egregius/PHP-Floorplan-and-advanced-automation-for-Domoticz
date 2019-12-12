<?php
/**
 * Pass2PHP
 * php version 7.3.11-1
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
if ($status=='On') {
    sl('lichtbadkamer', 22, basename(__FILE__).':'.__LINE__);
    store('deurbadkamer', $d['deurbadkamer']['s'], basename(__FILE__).':'.__LINE__);
    douche();
    resetsecurity();
    if ($d['bose102']['s']=='Off') bosezone(102);
   	if ($d['bose102']['m']==0) {
   		boseplayinfo(saytime().sayweather());
   		storemode('bose102', 1);
   	}
}
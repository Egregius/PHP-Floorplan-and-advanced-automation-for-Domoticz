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
if ($status=='On') {
    sl('lichtbadkamer', 34, basename(__FILE__).':'.__LINE__);
    store('deurbadkamer', $d['deurbadkamer']['s'], basename(__FILE__).':'.__LINE__);
    douche();
    resetsecurity();
    if (TIME>=strtotime('5:30')&&TIME<strtotime('21:30')) {
    	if ($d['bose102']['s']=='Off') bosezone(102);
    }
}
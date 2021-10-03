<?php
/**
 * Pass2PHP
 * php version 7.3
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link	 https://egregius.be
 **/
if ($status=='On') {
	store('deurbadkamer', $d['deurbadkamer']['s'], basename(__FILE__).':'.__LINE__);
	sl('lichtbadkamer', 60, basename(__FILE__).':'.__LINE__);
	douche();
	if (TIME>=strtotime('5:30')&&TIME<strtotime('21:30')) {
		if ($d['bose102']['s']=='Off') bosezone(102);
	}
}

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
if ($status=='Open'&&$d['daikin']['m']==0&&$d['daikin']['s']=='On') {
	if ($heating<0) daikinset('alex', 0, 3, 20, basename(__FILE__).':'.__LINE__, 'A', 40);
	else daikinset('alex', 0, 4, 13, basename(__FILE__).':'.__LINE__, 'A', 40);
}

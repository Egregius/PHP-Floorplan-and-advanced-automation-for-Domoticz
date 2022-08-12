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
if ($status=='Open'&&TIME>strtotime('6:00')&&TIME<strtotime('10:00')) {
    if ($d['Ralex']['s']>1) sl('Ralex', 1, basename(__FILE__).':'.__LINE__);
}
if ($status=='Open'&&$d['daikin']['m']==0&&$d['daikin']['s']=='On') daikinset('alex', 0, 3, 20, basename(__FILE__).':'.__LINE__, 'A', 40);

<?php
/**
 * Pass2PHP
 * php version 7.3.4-2
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
if ($status=='Open'&&TIME>strtotime('6:00')&&TIME<strtotime('12:00')) {
    if ($d['Rtobi']['m']!=0) storemode('Rtobi', 0, basename(__FILE__).':'.__LINE__);
    if ($d['Rtobi']['s']>0) sl('Rtobi', 0, basename(__FILE__).':'.__LINE__);
}
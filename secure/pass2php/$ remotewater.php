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
    if ($d['water']['s']=='Off') sw('water', 'On', basename(__FILE__).':'.__LINE__);
    if ($d['water']['m']==0) storemode('water', 300, basename(__FILE__).':'.__LINE__);
    elseif ($d['water']['m']==300) storemode('water', 1800, basename(__FILE__).':'.__LINE__);
    elseif ($d['water']['m']==1800) storemode('water', 7200, basename(__FILE__).':'.__LINE__);
} else {
    if ($d['water']['s']=='On') sw('water', 'Off', basename(__FILE__).':'.__LINE__);
}

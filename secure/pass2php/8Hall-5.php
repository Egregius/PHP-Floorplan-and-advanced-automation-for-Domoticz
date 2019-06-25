<?php
/**
 * Pass2PHP
 * php version 7.3.3-1
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
if ($d['Weg']['s']!=0) {
    store('Weg', 0, null, true);
}
if ($d['hall']['s']=='Off') {
    sw('hall', 'On', basename(__FILE__).':'.__LINE__);
}
resetsecurity();
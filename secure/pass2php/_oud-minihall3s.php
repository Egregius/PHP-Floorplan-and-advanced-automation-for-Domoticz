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
if ($d['Weg']['s']!=0) {
    store('Weg', 0, basename(__FILE__).':'.__LINE__);
}
if ($d['hall']['s']=='Off') {
    sw('hall', 'On', basename(__FILE__).':'.__LINE__);
}
resetsecurity();
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
sl('zolder', 10, basename(__FILE__).':'.__LINE__);
storemode('zolder', 1, basename(__FILE__).':'.__LINE__);
sw('bureeltobi', 'Off');
sw('tvtobi', 'Off');

if ($d['raamhall']['s']=='Closed') {
    sw('zoldertrap', 'On', basename(__FILE__).':'.__LINE__);
    store('Weg', 0, basename(__FILE__).':'.__LINE__);
}
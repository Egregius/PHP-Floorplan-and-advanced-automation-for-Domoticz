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
if ($status=='On'&&past('8badkamer-7')>5) {
    if ($d['badkamervuur1']['s']=='On') {
        store('badkamer_set', 10, basename(__FILE__).':'.__LINE__);
        if ($d['badkamervuur2']['s']=='On') sw('badkamervuur2', 'Off');
        sw('badkamervuur1', 'Off');
    } elseif ($d['heating']['s']!=1) {
        store('badkamer_set', 22.5, basename(__FILE__).':'.__LINE__);
        sw('badkamervuur1', 'On');
        if ($d['badkamervuur2']['s']=='Off') sw('badkamervuur2', 'On');
    }
    douche();
    resetsecurity();
}
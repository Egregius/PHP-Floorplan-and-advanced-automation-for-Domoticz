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
if ($status==0) {
    if ($d['pirinkom']['s']!='Off') {
        store('pirinkom', 'Off', basename(__FILE__).':'.__LINE__);
    }
}
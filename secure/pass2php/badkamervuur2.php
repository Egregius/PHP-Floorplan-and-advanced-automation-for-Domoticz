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
    if ($d['badkamervuur1']['s']!='On') {
        sw('badkamervuur1', 'On', basename(__FILE__).':'.__LINE__);
    }
}

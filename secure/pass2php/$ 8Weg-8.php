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
if ($status=='On') {
    store('Weg', 0, basename(__FILE__).':'.__LINE__);
    sw('poortrf', 'On', basename(__FILE__).':'.__LINE__);
    resetsecurity();
}
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
    if ($d['kamer']['s']==0) {
        sl('kamer', 1, basename(__FILE__).':'.__LINE__);
    } else {
        $new=floor($d['kamer']['s']*0.65);
        sl('kamer', $new, basename(__FILE__).':'.__LINE__);
    }
    resetsecurity();
}
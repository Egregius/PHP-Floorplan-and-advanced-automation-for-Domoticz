<?php
/**
 * Pass2PHP
 * php version 7.2.15
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
if ($status=='Off') {
    $status=0;
} elseif ($status=='On') {
    $status=100;
} else {
    $status=filter_var($status, FILTER_SANITIZE_NUMBER_INT);
}
if ($status==0) {
    storemode('kamer', 0);
}
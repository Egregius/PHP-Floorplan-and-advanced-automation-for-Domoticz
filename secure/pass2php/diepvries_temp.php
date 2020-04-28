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
if ($status> -15) {
    alert('Te warm in diepvries! '.$status.' °C', 1800);
}
$prev=$d['diepvries_temp']['s'];
if ($prev==$status) {
	die('notting to do');
}
$set=$d['diepvries_temp']['m'];
$tdiepvries=past('diepvries');
if ($status < $prev && $status <= $set && $tdiepvries > 3600 && $d['diepvries']['s']!='Off') {
    sw('diepvries', 'Off', basename(__FILE__).':'.__LINE__);
} elseif ($status > $prev && $status >= $set && $tdiepvries > 3600 && $d['diepvries']['s']!='On') {
    sw('diepvries', 'On', basename(__FILE__).':'.__LINE__);
}


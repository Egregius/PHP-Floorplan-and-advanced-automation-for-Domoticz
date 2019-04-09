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
$status=explode(';', $status);
$status=$status[0];

$room='badkamer';
$prev=$d[$room.'_temp']['s'];
$set=$d[$room.'_set']['s'];
$tbadkamervuur=$d['badkamervuur1']['t'];
$d[$room.'_temp']['s']=$status;
if ($status>$prev&&$status>$set&&$tbadkamervuur<time-600) {
    sw('badkamervuur2', 'Off');
    sw('badkamervuur1', 'Off');
} elseif ($status<$prev&&$status<$set&&$tbadkamervuur<time-600) {
    sw('badkamervuur1', 'On');
} else {
    include '_verwarming.php';
}
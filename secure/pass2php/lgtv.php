<?php
/**
 * Pass2PHP
 * php version 8.0
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
if ($status=='On') {
    lgcommand('on');
    lg('lgtv.php On');
} elseif ($status=='Off') {
    lgcommand('off');
    lg('lgtv.php Off');
}

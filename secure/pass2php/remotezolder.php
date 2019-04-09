<?php
/**
 * Pass2PHP
 * php version 7.0.33
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
if ($status=='On') {
    include dirname(__DIR__) . '/secure/pass2php/minihall2s.php';
} else {
    include dirname(__DIR__) . '/secure/pass2php/minihall4s.php';
}
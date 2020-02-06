<?php
/**
 * Pass2PHP
 * php version 7.3.11-1
 *
 * This is a receiver for ifttt webhooks.
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
require 'secure/functions.php';
telegram('IFTTT'.PHP_EOL.print_r($_REQUEST, true));

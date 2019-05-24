<?php
/**
 * Pass2PHP cron trigger script
 * php version 7.3.3-1
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
require '/var/www/config.php';
require 'functions.php';
$d=fetchdata();
if (isset($_REQUEST['rolluiken'])) {
    $username='rolluiken';
    include '_rolluiken.php';
}
if (isset($_REQUEST['verwarming'])) {
    $username='verwarming';
    include '_verwarming.php';
}
if (isset($_REQUEST['cron10'])) {
    $username='cron10';
    include '_cron10.php';
}
if (isset($_REQUEST['cron60'])) {
    $username='cron60';
    include '_cron60.php';
}
if (isset($_REQUEST['cron120'])) {
    $username='cron120';
    include '_cron120.php';
}
if (isset($_REQUEST['cron180'])) {
    $username='cron180';
    include '_cron180.php';
    $username='gcal';
    include 'gcal/gcal.php';
    sleep(2);
    include 'gcal/verlof.php';
    sleep(2);
    $username='gcaltobibeitem';
    include 'gcal/tobibeitem.php';
    sleep(2);
    $username='gcalmirom';
    include 'gcal/mirom.php';
    sleep(2);
}
if (isset($_REQUEST['cron3600'])) {
    $username='cron3600';
    include '_cron3600.php';
}
if (isset($_REQUEST['test'])) {
    $username='test';
    include 'gcal/gcal.php';
}

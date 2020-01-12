<?php
/**
 * Pass2PHP cron trigger script
 * php version 7.3.11-1
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
require 'functions.php';
//lg('cron');
$d=fetchdata();
if (isset($_REQUEST['rolluiken'])) {
    include '_rolluiken.php';
}
if (isset($_REQUEST['tempcontrol'])) {
    include '_tempcontrol.php';
}
if (isset($_REQUEST['cron10'])) {
    include '_cron10.php';
}
if (isset($_REQUEST['cron60'])) {
    include '_cron60.php';
}
if (isset($_REQUEST['cron120'])) {
    include '_cron120.php';
}
if (isset($_REQUEST['cron180'])) {
    include '_cron180.php';
    include 'gcal/gcal.php';
    include 'gcal/verlof.php';
    include 'gcal/tobibeitem.php';
    include 'gcal/mirom.php';
}
if (isset($_REQUEST['cron240'])) {
    include '_cron240.php';
}
if (isset($_REQUEST['cron300'])) {
    include '_cron300.php';
}
if (isset($_REQUEST['cron3600'])) {
    include '_cron3600.php';
}
if (isset($_REQUEST['weather'])) {
    include '_weather.php';
}
if (isset($_REQUEST['test'])) {
    include 'gcal/gcal.php';
}
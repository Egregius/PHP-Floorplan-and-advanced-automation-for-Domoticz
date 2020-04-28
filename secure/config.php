<?php
/**
 * Pass2PHP
 * php version 7.3
 *
 * Example config file
 * Can be placed in /var/www/config.php
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
error_reporting(E_ALL);
ini_set("display_errors", "on");
date_default_timezone_set('Europe/Brussels');
if (!defined('TIME')) {
    define('TIME', $_SERVER['REQUEST_TIME']);
}
$dbname='domotica';
$dbuser='domotica';
$dbpass='fount-bonn-Subside-7protegee-7Howl-jerk-nerd8-8courier-aftermost-eldest7-Devon-9Sect-catnap-Evans-8hypnotic';


$log=false;
$page=basename($_SERVER['PHP_SELF']);

$domoticzurl='http://127.0.0.1:8080';
$denonurl='192.168.2.6';
$lgtvip='192.168.2.27';
$boseip2='192.168.2.2';
$boseip3='192.168.2.3';
$boseip4='192.168.2.4';

$LogFile='/var/log/floorplanlog.log';
$users=array('user1'=>'pass1','user2'=>'pass2','user3'=>'pass3');
$cookie='CookieName';

$smappeeclient_id='smappeeclient_id';
$smappeeclient_secret = 'smappeeclient_secret';
$smappeeusername='smappeeusername';
$smappeepassword='smappeepassword';
$smappeeserviceLocationId=123456;

$dsapikey='2e43c9ed62ff79afc81f329245203a10';
$owappid='dc3486b0bf1a02a41d2521db6515821d';
$owid=1234567;
$lat=51.2930154;
$lon=3.4123163;

$calendarApp='GoogleCalendarAppname';
$calendarId='uze9pqcqnahmdc3u1aeris16h8@group.calendar.google.com';
$calendarIdMirom='dsjknfaia2dg8obe8eol046vm4@group.calendar.google.com';
$calendarIdTobi='email@gmail.com';
$calendarIdVerlof='otheremail@gmail.com';

$telegrambot='123456789:ABCD-eFGhI-JKfMqICiJs8q9A_3YIr9irxI';
$telegramchatid1=12345678;
$telegramchatid2=23456789;
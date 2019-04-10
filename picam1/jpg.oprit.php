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
require '../secure/settings.php';
if ($home===true) {
    $boundary="PIderman";
    header("Content-type: multipart/x-mixed-replace; boundary=$boundary");
    header("Cache-Control: no-cache");
    header("Pragma: no-cache");
    header("Connection: close");
    ob_flush();
    set_time_limit(0);
    ob_start();
    echo "--$boundary\r\n";
    echo "Content-type: image/jpeg\r\n";
    $fileContents=file_get_contents("http://192.168.2.13/mjpeg_read.php");
    $fileLength=strlen($fileContents);
    echo "Content-Length:".$fileLength."\r\n";
    echo "\r\n";
    echo $fileContents;
    echo "\r\n";
    ob_end_flush();
}
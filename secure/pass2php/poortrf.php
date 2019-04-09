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
    for ($k=1;$k<=60;$k++) {
        file_get_contents('http://192.168.2.13/fifo_command.php?cmd=motion_enable%20off');
        if ($http_response_header[0]=='HTTP/1.1 200 OK') {
            break;
        }
        sleep(5);
    }
    for ($k=1;$k<=60;$k++) {
        file_get_contents('http://192.168.2.11/fifo_command.php?cmd=motion_enable%20off');
        if ($http_response_header[0]=='HTTP/1.1 200 OK') {
            break;
        }
        sleep(5);
    }
} else {
    for ($k=1;$k<=60;$k++) {
        file_get_contents('http://192.168.2.13/fifo_command.php?cmd=motion_enable%20on');
        if ($http_response_header[0]=='HTTP/1.1 200 OK') {
            break;
        }
        sleep(5);
    }
    for ($k=1;$k<=60;$k++) {
        file_get_contents('http://192.168.2.11/fifo_command.php?cmd=motion_enable%20on');
        if ($http_response_header[0]=='HTTP/1.1 200 OK') {
            break;
        }
        sleep(5);
    }
}
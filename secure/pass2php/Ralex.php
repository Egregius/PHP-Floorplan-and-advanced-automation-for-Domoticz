<?php
/**
 * Pass2PHP
 * php version 7.3.4-2
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
if (isset($status)&&$status=='Open'&&TIME>strtotime('6:00')&&TIME<strtotime('12:00')) {
    picamoff();
} elseif (isset($status)&&$status=='Closed'&&TIME>strtotime('18:00')) {
	sw('picam2plug', 'On', basename(__FILE__).':'.__LINE__);
} elseif (isset($_REQUEST['Off'])) {
	picamoff();
}

function picamoff()
{
    file_get_contents('http://192.168.2.12/fifo_command.php?cmd=halt');
    sleep(1);
    file_get_contents('http://192.168.2.12/fifo_command.php?cmd=halt');
    sleep(10);
    sw('picam2plug', 'Off', basename(__FILE__).':'.__LINE__);
}
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
if (isset($_REQUEST['script'])) {
    shell_exec('./'.$_REQUEST['script'].'.sh');
    //telegram($_REQUEST['script'].' ececuted');
}
function telegram($msg)
{
    $telegrambot='113592115:AAEZ-xCRhO-RBfUqICiJs8q9A_3YIr9irxI';
    $silent=true;
    $telegramchatid=55975443;
    for ($x=1;$x<=100;$x++) {
        $result=json_decode(file_get_contents('https://api.telegram.org/bot'.$telegrambot.'/sendMessage?chat_id='.$telegramchatid.'&text='.urlencode($msg).'&disable_notification='.$silent));
        if (isset($result->ok)) {
            if ($result->ok===true) {
                break;
            }
        }
    }
}
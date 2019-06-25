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
require_once '/var/www/config.php';
$silent=true;
$to=null;
if (isset($_REQUEST['silent'])) {
    $silent=$_REQUEST['silent'];
}
if (isset($_REQUEST['text'])) {
    $content=str_replace('__', PHP_EOL, $_REQUEST['text']);
}
if (isset($_REQUEST['to'])) {
    $to=$_REQUEST['to'];
}
if (!empty($argv[1])&&!empty($argv[2])) {
    $content=$argv[2];
}

if (isset($content)) {
    $bot_url="https://api.telegram.org/bot".$telegrambot."/";
    $url=$bot_url."sendMessage?chat_id=".$telegramchatid1;
    $post_fields=array(
        'chat_id'=>$telegramchatid1,
        'text'=>$content,
        'disable_notification'=>$silent
    );
    $ch=curl_init();
    curl_setopt(
        $ch,
        CURLOPT_HTTPHEADER,
        array("Content-Type:multipart/form-data")
    );
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);
    for ($x=1;$x<=10;$x++) {
        $result=json_decode(curl_exec($ch), true);
        if ($result['ok']===true) {
            break;
        }
        sleep($x*2);
    }
    if ($to=='2') {
        $url=$bot_url."sendMessage?chat_id=".$telegramchatid2;
        $post_fields=array(
            'chat_id'=>$telegramchatid2,
            'text'=>$content,
            'disable_notification'=>$silent
        );
        $ch=curl_init();
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array("Content-Type:multipart/form-data")
        );
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);
        for ($x=1;$x<=10;$x++) {
            $result=json_decode(curl_exec($ch), true);
            if ($result['ok']===true) {
                break;
            }
            sleep($x*2);
        }
    }
}
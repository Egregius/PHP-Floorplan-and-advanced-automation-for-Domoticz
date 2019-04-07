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
require '/var/www/config.php';
$silent=true;
$type='text';
$to=null;
if (isset($_REQUEST['silent'])) {
    $silent=$_REQUEST['silent'];
}
if (isset($_REQUEST['text'])) {
    $type='text';
    $content=str_replace('__', PHP_EOL, $_REQUEST['text']);
}
if (isset($_REQUEST['photo'])) {
    $type='photo';
    $content=$_REQUEST['photo'];
}
if (isset($_REQUEST['video'])) {
    $type='video';
    $content=$_REQUEST['video'];
    $silent=true;
}
if (isset($_REQUEST['to'])) {
    $to=$_REQUEST['to'];
}
if (!empty($argv[1])&&!empty($argv[2])) {
    $type=$argv[1];$content=$argv[2];
}
if (!empty($argv[1])&&$argv[1]=="snapshot") {
    $type='photo';
    $content = "/run/pikrellcam/mjpeg.jpg";
    $silent=true;
}

if (isset($type)&&isset($content)) {
    $bot_url="https://api.telegram.org/bot".$telegrambot."/";
    if ($type=="text") {
        $url=$bot_url."sendMessage?chat_id=".$telegramchatid1;
        $post_fields=array('chat_id'=>$telegramchatid1,'text'=>$content,'disable_notification'=>$silent);
    } elseif ($type=="photo") {
        $url=$bot_url."sendPhoto?chat_id=".$telegramchatid1;
        $post_fields=array('chat_id'=>$telegramchatid1,'photo'=>new CURLFile(realpath($content)),'disable_notification'=>true);
    } elseif ($type=="video") {
        $url=$bot_url."sendVideo?chat_id=".$telegramchatid1;
        $post_fields=array('chat_id'=>$telegramchatid1,'video'=>new CURLFile(realpath($content)),'disable_notification'=>true);
    }
    $ch=curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:multipart/form-data"));
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    for ($x=1;$x<=100;$x++) {
        $result=json_decode(curl_exec($ch), true);
        if ($result['ok']===true) {
            break;
        }
        sleep($x*3);
    }
}
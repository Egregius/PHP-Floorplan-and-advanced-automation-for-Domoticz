<?php
if (isset($_REQUEST['alert'],$_REQUEST['text'],$_REQUEST['time'])) {
	include 'functions.php';
	if (isset($_REQUEST['silent'])) $silent=$_REQUEST['silent'];
	else $silent=true;
	alert($_REQUEST['alert'], str_replace('__', PHP_EOL, $_REQUEST['text']), $_REQUEST['time'], $silent);
	exit;
} else {
	require '/var/www/config.php';
}
$silent=true;
$to=1;
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
	echo $content;
	$bot_url="https://api.telegram.org/bot".$telegrambot."/";
	if ($to==1||$to==3) {
		$url=$bot_url."sendMessage?chat_id=".$telegramchatid1.'&disable_web_page_preview&parse_mode=html';
		$post_fields=array(
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
			if (is_array($result)&&$result['ok']===true) {
				break;
			}
			sleep($x*2);
		}
	}
	if ($to==2||$to==3) {
		$url=$bot_url."sendMessage?chat_id=".$telegramchatid2.'&disable_web_page_preview&parse_mode=html';
		$post_fields=array(
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
			if (is_array($result)&&$result['ok']===true) {
				break;
			}
			sleep($x*2);
		}
	}
}
